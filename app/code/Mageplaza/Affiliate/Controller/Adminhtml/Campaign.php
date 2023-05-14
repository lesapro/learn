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

namespace Mageplaza\Affiliate\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\Affiliate\Model\CampaignFactory;

/**
 * Class Campaign
 * @package Mageplaza\Affiliate\Controller\Adminhtml
 */
abstract class Campaign extends AbstractAction
{
    /**
     * @var CampaignFactory
     */
    protected $_campaignFactory;

    /**
     * Campaign constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $coreRegistry
     * @param CampaignFactory $campaignFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $coreRegistry,
        CampaignFactory $campaignFactory
    ) {
        $this->_campaignFactory = $campaignFactory;

        parent::__construct($context, $resultPageFactory, $coreRegistry);
    }

    /**
     * @return mixed
     */
    protected function _initCampaign()
    {
        $campaignId = (int)$this->getRequest()->getParam('id');
        /** @var \Mageplaza\Affiliate\Model\Campaign $campaign */
        $campaign = $this->_campaignFactory->create();
        if ($campaignId) {
            $campaign->load($campaignId);
            if (!$campaign->getId()) {
                $this->messageManager->addError(__('This campaign no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('affiliate/campaign/index');

                return $resultRedirect;
            }
        }

        return $campaign;
    }

    /**
     * is action allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mageplaza_Affiliate::campaign');
    }
}
