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

namespace Mageplaza\Affiliate\Controller\Adminhtml\Group;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\Affiliate\Model\Group;
use Mageplaza\Affiliate\Model\GroupFactory;
use RuntimeException;

/**
 * Class InlineEdit
 * @package Mageplaza\Affiliate\Controller\Adminhtml\Group
 */
class InlineEdit extends Action
{
    /**
     * @var JsonFactory
     */
    protected $_jsonFactory;

    /**
     * @var GroupFactory
     */
    protected $_groupFactory;

    /**
     * InlineEdit constructor.
     *
     * @param JsonFactory $jsonFactory
     * @param GroupFactory $groupFactory
     * @param Context $context
     */
    public function __construct(
        JsonFactory $jsonFactory,
        GroupFactory $groupFactory,
        Context $context
    ) {
        $this->_jsonFactory = $jsonFactory;
        $this->_groupFactory = $groupFactory;
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
        foreach (array_keys($postItems) as $groupId) {
            /** @var Group $group */
            $group = $this->_groupFactory->create()->load($groupId);
            try {
                $groupData = $postItems[$groupId];//todo: handle dates
                $group->addData($groupData);
                $group->save();
            } catch (LocalizedException $e) {
                $messages[] = $this->getErrorWithGroupId($group, $e->getMessage());
                $error = true;
            } catch (RuntimeException $e) {
                $messages[] = $this->getErrorWithGroupId($group, $e->getMessage());
                $error = true;
            } catch (Exception $e) {
                $messages[] = $this->getErrorWithGroupId(
                    $group,
                    __('Something went wrong while saving the Group.')
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
     * @param Group $group
     * @param $errorText
     *
     * @return string
     */
    protected function getErrorWithGroupId(Group $group, $errorText)
    {
        return '[Group ID: ' . $group->getId() . '] ' . $errorText;
    }
}
