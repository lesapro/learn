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

namespace Mageplaza\Affiliate\Controller\Adminhtml\Campaign;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\Affiliate\Controller\Adminhtml\Campaign;
use Mageplaza\Affiliate\Model\CampaignFactory;
use Magento\SalesRule\Helper\Coupon;
use Magento\Framework\Math\Random;
use Mageplaza\Affiliate\Helper\Data;

/**
 * Class Generate
 * @package Mageplaza\Affiliate\Controller\Adminhtml\Campaign
 */
class Generate extends Campaign
{
    /**
     * @var Coupon
     */
    private $couponHelper;

    /**
     * Generate constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $coreRegistry
     * @param CampaignFactory $campaignFactory
     * @param Coupon $couponHelper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $coreRegistry,
        CampaignFactory $campaignFactory,
        Coupon $couponHelper
    ) {
        $this->couponHelper = $couponHelper;

        parent::__construct($context, $resultPageFactory, $coreRegistry, $campaignFactory);
    }

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        try {
            $result = ['status' => 'success', 'coupon' => $this->generateCode()];
        } catch (LocalizedException $e) {
            $result = ['status' => 'error', 'message' => $e->getMessage()];
        }

        return $this->getResponse()->representJson(Data::jsonEncode($result));
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function generateCode()
    {
        $data   = $this->getRequest()->getParams();
        $length = (int)$data['code_length'];
        $format = $data['code_format'];
        if (empty($format)) {
            $format = Coupon::COUPON_FORMAT_ALPHANUMERIC;
        }

        $charset     = $this->couponHelper->getCharset($format);
        $code        = '';
        $charsetSize = count($charset);
        for ($i = 0; $i < $length; ++$i) {
            $char = $charset[Random::getRandomNumber(0, $charsetSize - 1)];
            $code .= $char;
        }

        return $code;
    }
}
