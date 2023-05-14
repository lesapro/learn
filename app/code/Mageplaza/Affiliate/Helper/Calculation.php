<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Affiliate
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Affiliate\Helper;

use Exception;
use Magento\Bundle\Model\Product\Type;
use Magento\Cms\Model\BlockFactory;
use Magento\Customer\Helper\View as CustomerViewHelper;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Math\Calculator;
use Magento\Framework\Math\CalculatorFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Item;
use Magento\Sales\Model\Order\Invoice;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Affiliate\Model\AccountFactory;
use Mageplaza\Affiliate\Model\Campaign;
use Mageplaza\Affiliate\Model\Campaign\Discount;
use Mageplaza\Affiliate\Model\CampaignFactory;
use Mageplaza\Affiliate\Model\TransactionFactory;
use Zend_Serializer_Exception;

/**
 * Class Calculation
 *
 * @package Mageplaza\Affiliate\Helper
 */
class Calculation extends Data
{
    /**
     * @var DataObject
     */
    protected $_address;

    /**
     * Calculator instances for delta rounding of prices
     *
     * @var Calculator[]
     */
    protected $_calculators = [];

    /**
     * @var array
     */
    protected $_total = [];

    /**
     * @var ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var CalculatorFactory
     */
    protected $_calculatorFactory;

    /**
     * Calculation constructor.
     *
     * @param Context                $context
     * @param ObjectManagerInterface $objectManager
     * @param AccountFactory         $accountFactory
     * @param CampaignFactory        $campaignFactory
     * @param TransactionFactory     $transactionFactory
     * @param BlockFactory           $blockFactory
     * @param CustomerFactory        $customerFactory
     * @param CookieManagerInterface $cookieManagerInterface
     * @param CustomerSession        $customerSession
     * @param CookieMetadataFactory  $cookieMetadataFactory
     * @param PriceCurrencyInterface $priceCurrency
     * @param StoreManagerInterface  $storeManager
     * @param TransportBuilder       $transportBuilder
     * @param CustomerViewHelper     $customerViewHelper
     * @param LayoutInterface        $layout
     * @param Registry               $registry
     * @param CalculatorFactory      $calculatorFactory
     * @param ManagerInterface       $messageManager
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        AccountFactory $accountFactory,
        CampaignFactory $campaignFactory,
        TransactionFactory $transactionFactory,
        BlockFactory $blockFactory,
        CustomerFactory $customerFactory,
        CookieManagerInterface $cookieManagerInterface,
        CustomerSession $customerSession,
        CookieMetadataFactory $cookieMetadataFactory,
        PriceCurrencyInterface $priceCurrency,
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder,
        CustomerViewHelper $customerViewHelper,
        LayoutInterface $layout,
        Registry $registry,
        CalculatorFactory $calculatorFactory,
        ManagerInterface $messageManager
    ) {
        $this->_calculatorFactory = $calculatorFactory;
        $this->_messageManager    = $messageManager;

        parent::__construct(
            $context,
            $objectManager,
            $accountFactory,
            $campaignFactory,
            $transactionFactory,
            $blockFactory,
            $customerFactory,
            $cookieManagerInterface,
            $customerSession,
            $cookieMetadataFactory,
            $priceCurrency,
            $storeManager,
            $transportBuilder,
            $customerViewHelper,
            $layout,
            $registry
        );
    }

    /**
     * @param Quote                       $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total                       $total
     *
     * @return $this
     */
    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        $this->_address = $shippingAssignment->getShipping()->getAddress();
        $this->_total   = $total;

        return $this;
    }

    /**
     * @param       $storeId
     * @param false $isDiscount
     *
     * @return bool|int|void
     * @throws LocalizedException
     * @throws InputException
     * @throws FailureToSendException
     */
    public function canCalculate($storeId, $isDiscount = false)
    {
        // - only calculate discount if has key and do not has first order
        // - comission always calculated if has first order

        $key                = $this->getAffiliateKey(); // if no cookie then first order key
        $affSource          = $this->getAffiliateSourceFromCookie(self::AFFILIATE_COOKIE_SOURCE_NAME);
        $campaignCouponCode = null;
        if ($affSource === 'coupon') {
            $couponWithPreFix = explode('-', $key);
            if (count($couponWithPreFix) !== 2) {
                return false;
            }

            list($key, $campaignCouponCode) = $couponWithPreFix;
        }
        $account   = $this->getCurrentAffiliate(); // get aff acc base on current customer id
        $campaigns = [];

        if (!$this->registry->registry('mp_affiliate_account')) {
            $this->registry->register('mp_affiliate_account', $this->getAffiliateByKeyOrCode($key));
        }
        $refAcc = $this->registry->registry('mp_affiliate_account');
        if ($key && !$account->getId() && $refAcc->getId() && $refAcc->isActive()) {
            $campaigns = $this->getAvailableCampaign($refAcc, $campaignCouponCode);
        }
        if ($isDiscount) {
            if ($this->getAffiliateKeyFromCookie(self::AFFILIATE_COOKIE_SOURCE_NAME) === 'coupon') {
                if (!$this->isUseCodeAsCoupon($storeId)) {
                    $this->deleteAffiliateCookieSourceName();

                    return false;
                }
            } else {
                return count($campaigns) && !$this->hasFirstOrder();
            }
        }

        return count($campaigns);// && $this->hasFirstOrder();
    }

    /**
     * @param null        $account
     * @param string|null $campaignCouponCode
     *
     * @return mixed|null
     * @throws LocalizedException
     * @throws Zend_Serializer_Exception
     */
    public function getAvailableCampaign($account = null, $campaignCouponCode = null)
    {
        if ($account === null) {
            $account = $this->getCurrentAffiliate();
        }

        $cacheKey = 'affiliate_available_campaign_' . $account->getId();
        if (!self::hasCache($cacheKey)) {
            $campaignResult = [];
            if ($this->getAffiliateSourceFromCookie() === 'banner') {
                $campaign = $this->getCampaignRelatedToBanner();
                if ($campaign->validate($this->_address)) {
                    $campaignResult[] = $campaign;
                }
                self::saveCache($cacheKey, $campaignResult);

                return self::getCache($cacheKey);
            }

            $campaigns = $this->campaignFactory->create()->getCollection()
                ->getAvailableCampaign(
                    $account->getGroupId(),
                    $this->storeManager->getWebsite()->getId(),
                    $campaignCouponCode
                );
            /** @var Campaign $campaign */
            foreach ($campaigns as $campaign) {
                $campaign->setCommission($this->unserialize($campaign->getCommission()));
                if ($campaign->validate($this->_address)) {
                    $campaignResult[] = $campaign;
                }
            }

            self::saveCache($cacheKey, $campaignResult);
        }

        return self::getCache($cacheKey);
    }

    /**
     * Retrieve the campaign related to the banner via cookie value
     *
     * @return Campaign
     */
    public function getCampaignRelatedToBanner()
    {
        $bannerId   = $this->getAffiliateSourceValueFromCookie();
        $campaignId = $this->objectManager->create(
            'Mageplaza\AffiliatePro\Model\Banner'
        )->load($bannerId)->getCampaignId();

        return $this->campaignFactory->create()->load($campaignId);
    }

    /**
     * @param       $items
     * @param       $quote
     * @param array $quoteFields
     * @param array $itemFields
     */
    public function resetAffiliateData($items, $quote, $quoteFields = [], $itemFields = [])
    {
        $this->resetFields($quote, $quoteFields);
        foreach ($items as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                /** @var Item $child */
                foreach ($item->getChildren() as $child) {
                    $this->resetFields($child, $itemFields);
                }
            } else {
                $this->resetFields($item, $itemFields);
            }
        }
    }

    /**
     * @param $object ($quote || item)
     * @param $fields
     */
    public function resetFields($object, $fields)
    {
        foreach ($fields as $field) {
            $object->setData($field, 0);
        }
    }

    /**
     * @param      $quote
     * @param bool $isCalculateTax
     * @param bool $isCalculateAffDiscount
     *
     * @return int
     */
    public function getShippingTotalForDiscount($quote, $isCalculateTax = false, $isCalculateAffDiscount = true)
    {
        $total = 0;
        if (!$quote->getIsVirtual()) {
            $total = $this->_total->getBaseShippingAmount();
            if ($isCalculateTax) {
                $total = $this->_total->getBaseShippingInclTax();
            }
            $total -= $this->_total->getBaseShippingDiscountAmount();
            if ($isCalculateAffDiscount) {
                $total -= $quote->getAffiliateBaseDiscountShippingAmount();
            }
        }

        return $total;
    }

    /**
     * @param      $item
     * @param bool $isCalculateTax
     * @param bool $isCalculateAffDiscount
     *
     * @return mixed
     */
    public function getItemTotalForDiscount($item, $isCalculateTax = false, $isCalculateAffDiscount = true)
    {
        $total = $item->getBaseRowTotal() - $item->getBaseDiscountAmount();
        if ($isCalculateAffDiscount) {
            $total -= $item->getBaseAffiliateDiscountAmount();
        }
        if ($isCalculateTax) {
            $total += ($item->getBaseTaxAmount() + $item->getData('base_discount_tax_compensation_amount'));
        }

        return $total;
    }

    /**
     * @param      $items
     * @param      $quote
     * @param      $isCalculateShipping
     * @param      $isCalculateTax
     * @param bool $isCalculateAffDiscount
     *
     * @return int|mixed
     */
    public function getTotalOnCart(
        $items,
        $quote,
        $isCalculateShipping,
        $isCalculateTax,
        $isCalculateAffDiscount = true
    ) {
        $total = 0;
        foreach ($items as $item) {
            if ($item->getParentItem()) {
                continue;
            }

            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                /** @var Item $child */
                foreach ($item->getChildren() as $child) {
                    $total += $this->getItemTotalForDiscount($child, $isCalculateTax, $isCalculateAffDiscount);
                }
            } else {
                $total += $this->getItemTotalForDiscount($item, $isCalculateTax, $isCalculateAffDiscount);
            }
        }

        if ($isCalculateShipping) {
            $total += $this->getShippingTotalForDiscount($quote, $isCalculateTax, $isCalculateAffDiscount);
        }

        return $total;
    }

    /**
     * @param $campaign
     * @param $total
     *
     * @return float
     */
    public function getDiscountOnCampaign($campaign, $total)
    {
        $discount = $campaign->getDiscountAmount();
        if ((string)$campaign->getDiscountAction() === Discount::PERCENT) {
            $discount = $this->priceCurrency->round(($discount * $total) / 100);
        }

        return $discount;
    }

    /**
     * Round price or commission
     *
     * @param float  $price
     * @param string $type
     *
     * @return float
     */
    public function round($price, $type = 'regular')
    {
        if ($price) {
            if (!isset($this->_calculators[$type])) {
                $this->_calculators[$type] = $this->_calculatorFactory->create();
            }
            $price = $this->_calculators[$type]->deltaRound($price);
        }

        return $price;
    }

    /**
     * @param      $value
     * @param bool $format
     * @param bool $includeContainer
     * @param null $scope
     *
     * @return float|string
     */
    public function convertPrice($value, $format = true, $includeContainer = true, $scope = null)
    {
        return $format
            ? $this->priceCurrency->convertAndFormat(
                $value,
                $includeContainer,
                PriceCurrencyInterface::DEFAULT_PRECISION,
                $scope
            )
            : $this->priceCurrency->convert($value, $scope);
    }

    /**
     * @param       $object ($invoice | $creditmemo )
     * @param array $fields
     */
    public function calculateAffiliateDiscount($object, $fields = [])
    {
        $order                   = $object->getOrder();
        $affiliateDiscounted     = $order->getData($fields[0]);
        $baseAffiliateDiscounted = $order->getData($fields[1]);
        $totalDiscountAmount     = 0;
        $baseTotalDiscountAmount = 0;
        $addShippingDiscount     = true;
        if ($object instanceof Invoice) {
            foreach ($object->getOrder()->getInvoiceCollection() as $previousInvoice) {
                if ($previousInvoice->getDiscountAmount()) {
                    $addShippingDiscount = false;
                }
            }
        } else {
            $addShippingDiscount = false;
            if ($order->getShippingRefunded() < $order->getShippingAmount()) {
                if (abs(($object->getShippingAmount() + $order->getShippingRefunded())
                        - $order->getShippingAmount()) < 0.00001) {
                    $addShippingDiscount = true;
                }
            }
        }

        if ($addShippingDiscount) {
            $totalDiscountAmount     += $order->getAffiliateDiscountShippingAmount();
            $baseTotalDiscountAmount += $order->getBaseAffiliateDiscountShippingAmount();
        }

        foreach ($object->getAllItems() as $item) {
            $orderItem = $item->getOrderItem();
            if ($orderItem->isDummy()) {
                continue;
            }

            $orderItemQty     = $orderItem->getQtyOrdered();
            $itemDiscount     = $orderItem->getAffiliateDiscountAmount();
            $itemBaseDiscount = $orderItem->getBaseAffiliateDiscountAmount();
            if ($orderItemQty && $itemBaseDiscount > 0 && $item->getQty() > 0) {
                $itemPercent = $item->getQty() / $orderItemQty;

                $itemBaseDiscount = $object->roundPrice($itemPercent * $itemBaseDiscount, 'aff_base');
                if ($baseAffiliateDiscounted + $baseTotalDiscountAmount + $itemBaseDiscount
                    > $order->getBaseAffiliateDiscountAmount()) {
                    $itemBaseDiscount = $order->getBaseAffiliateDiscountAmount() - $baseAffiliateDiscounted;
                }

                $itemDiscount = $object->roundPrice($itemPercent * $itemDiscount, 'aff');
                if ($affiliateDiscounted + $totalDiscountAmount + $itemDiscount
                    > $order->getAffiliateDiscountAmount()) {
                    $itemDiscount = $order->getAffiliateDiscountAmount() - $affiliateDiscounted;
                }

                $item->setBaseAffiliateDiscountAmount($itemBaseDiscount)
                    ->setAffiliateDiscountAmount($itemDiscount);
                $totalDiscountAmount     += $itemDiscount;
                $baseTotalDiscountAmount += $itemBaseDiscount;
            }
        }

        $order->setData($fields[0], $affiliateDiscounted + $totalDiscountAmount);
        $order->setData($fields[1], $baseAffiliateDiscounted + $baseTotalDiscountAmount);

        $object->setBaseAffiliateDiscountAmount($baseTotalDiscountAmount);
        $object->setAffiliateDiscountAmount($totalDiscountAmount);

        $object->setBaseGrandTotal($object->getBaseGrandTotal() - $baseTotalDiscountAmount);
        $object->setGrandTotal($object->getGrandTotal() - $totalDiscountAmount);
    }

    /**
     * @param $totalTierCommission
     * @param $tierId
     * @param $tierCommission
     */
    public function setTierCommission(&$totalTierCommission, $tierId, $tierCommission)
    {
        if (isset($totalTierCommission[$tierId])) {
            $totalTierCommission[$tierId] += $tierCommission;
        } else {
            $totalTierCommission[$tierId] = $tierCommission;
        }
    }

    /**
     * @param      $totalTierCommission
     * @param      $commission
     * @param null $percentQty
     */
    public function setTotalTierCommission(&$totalTierCommission, $commission, $percentQty = null)
    {
        foreach ($commission as $tierId => $tierCommission) {
            if ($percentQty) {
                $tierCommission = $percentQty * $tierCommission;
            }

            $this->setTierCommission($totalTierCommission, $tierId, $tierCommission);
        }
    }

    /**
     * @param $commission
     *
     * @return array
     */
    public function getTotalTierCommission($commission)
    {
        $totalTierCommission = [];
        if (is_array($commission)) {
            foreach ($commission as $campaign) {
                foreach ($campaign as $tierId => $tierCommission) {
                    $this->setTierCommission($totalTierCommission, $tierId, $tierCommission);
                }
            }
        }

        return $totalTierCommission;
    }

    /**
     * @param $commission
     *
     * @return array|mixed
     * @throws Zend_Serializer_Exception
     */
    public function parseCommissionOnTier($commission)
    {
        $commission = $this->unserialize($commission);
        $commission = $this->getTotalTierCommission($commission);

        return $commission;
    }

    /**
     * @param $object
     * @param $field
     * @param $action
     *
     * @return array
     * @throws Zend_Serializer_Exception
     */
    public function calculateCommissionOrder($object, $field, $action)
    {
        $totalTierCommission = [];
        $order               = $object->getOrder();
        if ($object->getOrigData('entity_id') === null && $order->getAffiliateCommission()) {
            $orderCommission        = $this->parseCommissionOnTier($order->getAffiliateCommission());
            $orderCommissionEarned  = $this->unserialize($order->getData($field));
            $isAddHoldingCommission = false;
            $orderCommissionHolding = '';
            if ((string)$field === 'affiliate_commission_refunded') {
                $isAddHoldingCommission = true;
                $orderCommissionHolding = $this->unserialize($order->getAffiliateCommissionHoldingRefunded());
                if (!is_array($orderCommissionHolding)) {
                    $orderCommissionHolding = [];
                }
            }
            if (!is_array($orderCommissionEarned)) {
                $orderCommissionEarned = [];
            }

            $isAddShippingCommission = false;
            if ($order->getAffiliateShippingCommission()) {
                if ($object instanceof Invoice) {
                    if ($object->getShippingAmount() == $order->getShippingInvoiced()) {
                        $isAddShippingCommission = true;
                    }
                } else {
                    if ($object->getShippingAmount() == $order->getShippingRefunded()) {
                        $isAddShippingCommission = true;
                    }
                }
            }

            if ($isAddShippingCommission) {
                $shippingCommission = $this->parseCommissionOnTier($order->getAffiliateShippingCommission());
                foreach ($shippingCommission as $tierId => $tierCommission) {
                    $this->setTierCommission($totalTierCommission, $tierId, $tierCommission);
                    $this->setTierCommission($orderCommissionEarned, $tierId, $tierCommission);
                    if ($isAddHoldingCommission) {
                        $this->setTierCommission($orderCommissionHolding, $tierCommission, $tierCommission);
                    }
                }
            }

            foreach ($object->getItems() as $item) {
                $orderItem = $item->getOrderItem();
                if ($orderItem->getProductType() === Type::TYPE_CODE) {
                    continue;
                }

                if ($orderItem->getAffiliateCommission()) {
                    $itemCommission = $this->unserialize($orderItem->getAffiliateCommission());
                    $qty            = $item->getQty();
                    if ($itemCommission && $qty > 0) {
                        $totalItemCommission = $this->getTotalTierCommission($itemCommission);
                        foreach ($totalItemCommission as $tierId => $tierCommission) {
                            $tierCommission = $object->roundPrice(
                                ($qty / $orderItem->getQtyOrdered()) * $tierCommission,
                                'aff_commission'
                            );

                            if (!isset($orderCommissionEarned[$tierId])) {
                                $orderCommissionEarned[$tierId] = 0;
                            }

                            if ($orderCommissionEarned[$tierId] + $tierCommission > $orderCommission[$tierId]) {
                                $tierCommission = $orderCommission[$tierId] - $orderCommissionEarned[$tierId];
                            }

                            $orderCommissionEarned[$tierId] += $tierCommission;
                            if ($isAddHoldingCommission) {
                                if (!isset($orderCommissionHolding[$tierId])) {
                                    $orderCommissionHolding[$tierId] = 0;
                                }
                                $orderCommissionHolding[$tierId] += $tierCommission;
                            }

                            $this->setTierCommission($totalTierCommission, $tierId, $tierCommission);
                        }
                    }
                }
            }

            $order->setData($field, $this->serialize($orderCommissionEarned));
            if ($isAddHoldingCommission) {
                $order->setAffiliateCommissionHoldingRefunded($this->serialize($orderCommissionHolding));
            }

            $order->save();
            $this->createTransactionByAction($object, $totalTierCommission, $action);
        }

        return $totalTierCommission;
    }

    /**
     * @param $object
     * @param $totalTierCommission
     * @param $action
     */
    public function createTransactionByAction($object, $totalTierCommission, $action)
    {
        if (is_array($totalTierCommission) && count($totalTierCommission)) {
            foreach ($totalTierCommission as $id => $com) {
                $account = $this->accountFactory->create()->load($id);
                if ($account->getId()) {
                    $object->setCommissionAmount($com);
                    try {
                        $this->transactionFactory->create()->createTransaction($action, $account, $object);
                    } catch (Exception $e) {
                        $this->_messageManager->addErrorMessage(__('Something went wrong when creating transaction!'));
                    }
                }
            }
        }
    }
}
