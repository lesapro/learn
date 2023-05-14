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

namespace Mageplaza\Affiliate\Block;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\Affiliate\Model\Account\Status;
use Mageplaza\Affiliate\Model\Campaign\Display;

/**
 * Class Dashboard
 * @package Mageplaza\Affiliate\Block
 */
class Dashboard extends Account
{
    /**
     * @return mixed
     * @throws LocalizedException
     */
    public function getCampaigns()
    {
        $affiliateGroupId = $this->_affiliateHelper->getCurrentAffiliate()->getGroupId() ?: null;
        $campaigns        = $this->campaignFactory->create()->getCollection();
        $campaigns->getAvailableCampaign($affiliateGroupId, $this->_storeManager->getWebsite()->getId());
        if (!$this->isAffiliateLogin()) {
            $campaigns->addFieldToFilter('display', Display::ALLOW_GUEST);
        }

        return $campaigns;
    }

    /**
     * @return bool
     */
    public function isAffiliateLogin()
    {
        $affAccount = $this->accountFactory->create()->load($this->customerSession->getCustomerId(), 'customer_id');

        return $affAccount->getId();
    }

    /**
     * @return bool
     */
    public function isActiveAffiliateAccount()
    {
        $affAccount = $this->accountFactory->create()->load($this->customerSession->getCustomerId(), 'customer_id');

        return (int)$affAccount->getStatus() === Status::ACTIVE;
    }

    /**
     * @param number $rowSpan
     * @param mixed $campaign
     *
     * @return mixed
     */
    public function getCampaignRowSpan($rowSpan, $campaign)
    {
        $container = new DataObject(
            [
                'row_span' => ($this->isActiveAffiliateAccount() && $campaign->getCouponCode())
                    ? ($rowSpan + 3) : ($rowSpan + 2),
                'campaign' => $campaign
            ]
        );
        $this->_eventManager->dispatch('mageplaza_affiliate_dashboard_campaign_row_span', [
            'container' => $container
        ]);

        return $container->getRowSpan();
    }

    /**
     * @param string $name
     * @param mixed $campaign
     *
     * @return mixed|string
     */
    public function getCommissionCampaignAddition($name, $campaign)
    {
        $child = $this->getChild($name);
        if ($child) {
            $child->setCampaign($campaign);
            if (!$this->hasData('commission_campaign_addition_' . $campaign->getId())) {
                $this->setData('commission_campaign_addition_' . $campaign->getId(), $child->toHtml());

                return $this->getData('commission_campaign_addition_' . $campaign->getId());
            }
        }

        return '';
    }

    /**
     * @return mixed
     */
    public function getCouponPrefix()
    {
        return $this->getCurrentAccount()->getCode();
    }

    /**
     * @return string
     */
    public function getSavePrefixUrl()
    {
        return $this->getUrl('*/account/saveCouponPrefix');
    }

    /**
     * @return bool
     */
    public function hasCouponCode()
    {
        try {
            foreach ($this->getCampaigns() as $campaign) {
                if ($campaign->getCouponCode()) {
                    return true;
                }
            }
        } catch (LocalizedException $e) {
            $this->_logger->critical($e->getMessage());

            return false;
        }

        return false;
    }
}
