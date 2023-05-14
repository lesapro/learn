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
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\Affiliate\Model\Campaign;
use Mageplaza\Affiliate\Model\CampaignFactory;
use RuntimeException;

/**
 * Class InlineEdit
 * @package Mageplaza\Affiliate\Controller\Adminhtml\Campaign
 */
class InlineEdit extends Action
{
    /**
     * @var JsonFactory
     */
    protected $_jsonFactory;

    /**
     * @var CampaignFactory
     */
    protected $_campaignFactory;

    /**
     * InlineEdit constructor.
     *
     * @param JsonFactory $jsonFactory
     * @param CampaignFactory $campaignFactory
     * @param Context $context
     */
    public function __construct(
        JsonFactory $jsonFactory,
        CampaignFactory $campaignFactory,
        Context $context
    ) {
        $this->_jsonFactory = $jsonFactory;
        $this->_campaignFactory = $campaignFactory;
        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->_jsonFactory->create();
        $error = false;
        $messages = [];
        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }
        foreach (array_keys($postItems) as $campaignId) {
            /** @var Campaign $campaign */
            $campaign = $this->_campaignFactory->create()->load($campaignId);
            try {
                $campaignData = $postItems[$campaignId];//todo: handle dates
                $campaign->addData($campaignData);
                $campaign->save();
            } catch (LocalizedException $e) {
                $messages[] = $this->getErrorWithCampaignId($campaign, $e->getMessage());
                $error = true;
            } catch (RuntimeException $e) {
                $messages[] = $this->getErrorWithCampaignId($campaign, $e->getMessage());
                $error = true;
            } catch (Exception $e) {
                $messages[] = $this->getErrorWithCampaignId(
                    $campaign,
                    __('Something went wrong while saving the Campaign.')
                );
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * @param Campaign $campaign
     * @param $errorText
     *
     * @return string
     */
    protected function getErrorWithCampaignId(Campaign $campaign, $errorText)
    {
        return '[Campaign ID: ' . $campaign->getId() . '] ' . $errorText;
    }
}
