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

use Exception;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Mageplaza\Affiliate\Controller\Adminhtml\Campaign;

/**
 * Class Delete
 * @package Mageplaza\Affiliate\Controller\Adminhtml\Campaign
 */
class Delete extends Campaign
{
    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('campaign_id');
        if ($id) {
            try {
                /** @var \Mageplaza\Affiliate\Model\Campaign $campaign */
                $campaign = $this->_campaignFactory->create();
                $campaign->load($id);
                $campaign->delete();

                $this->messageManager->addSuccess(__('The Campaign has been deleted.'));
                $resultRedirect->setPath('affiliate/*/');

                return $resultRedirect;
            } catch (Exception $e) {
                $this->messageManager->addError($e->getMessage());

                // go back to edit form
                $resultRedirect->setPath('affiliate/*/edit', ['id' => $id]);

                return $resultRedirect;
            }
        }
        // display error message
        $this->messageManager->addError(__('Campaign to delete was not found.'));

        $resultRedirect->setPath('affiliate/*/');

        return $resultRedirect;
    }
}
