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

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Mageplaza\Affiliate\Controller\Adminhtml\Campaign;

/**
 * Class Edit
 * @package Mageplaza\Affiliate\Controller\Adminhtml\Campaign
 */
class Edit extends Campaign
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page|ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        /** @var \Mageplaza\Affiliate\Model\Campaign $campaign */
        $campaign = $this->_initCampaign();
        /** @var \Magento\Backend\Model\View\Result\Page|Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Mageplaza_Affiliate::campaign');
        $resultPage->getConfig()->getTitle()->set(__('Campaigns'));

        $title = $campaign->getId() ? __('Edit Campaign "%1"', $campaign->getName()) : __('New Campaign');
        $resultPage->getConfig()->getTitle()->prepend($title);

        $data = $this->_getSession()->getData('affiliate_campaign_data', true);
        if (!empty($data)) {
            $campaign->setData($data);
        }
        $this->_coreRegistry->register('current_campaign', $campaign);

        $model = $this->_objectManager->create('Magento\SalesRule\Model\Rule');
        if (!empty($campaign->getData())) {
            $model->addData($campaign->getData());
        }
        $model->getConditions()->setJsFormObject('rule_conditions_fieldset');
        $model->getActions()->setJsFormObject('rule_actions_fieldset');

        $this->_coreRegistry->register('current_campaign_rule', $model);

        return $resultPage;
    }
}
