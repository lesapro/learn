<?php

namespace Isobar\OneFieldName\Plugin\Magento\Customer\Model;

use Isobar\OneFieldName\Helper\Data as DataHelper;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\ValidationResultsInterface;
use Magento\Customer\Model\AccountManagement as BaseAccountManagement;
use Magento\Eav\Model\Validator\Attribute\Backend;
use Magento\Framework\App\ObjectManager;

/**
 * Class AccountManagement
 * @package Isobar\OneFieldName\Plugin\Magento\Customer\Model
 */
class AccountManagement
{
    /**
     * @var Backend
     */
    protected $eavValidator;

    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * AccountManagement constructor.
     *
     * @param DataHelper $dataHelper
     */
    public function __construct(
        DataHelper $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
    }

    /**
     * Validate customer data.
     *
     * @param BaseAccountManagement $subject
     * @param ValidationResultsInterface $validationResults
     * @param CustomerInterface $customer
     *
     * @return ValidationResultsInterface|string[]
     */
    public function afterValidate(
        BaseAccountManagement $subject,
        ValidationResultsInterface $validationResults,
        CustomerInterface $customer
    ) {
        $message = $this->getEavValidator()->getMessages();
        if ($validationResults->isValid() === false
            && isset($message[CustomerInterface::LASTNAME])
            && $this->dataHelper->isShowOneFieldName($customer->getWebsiteId())
        ) {
            unset($message[CustomerInterface::LASTNAME]);
            if (!empty($message)) {
                return $validationResults->setIsValid(false)->setMessages(
                    call_user_func_array(
                        'array_merge',
                        $message
                    )
                );
            }
            return $validationResults->setIsValid(true)->setMessages([]);
        }
        return $validationResults;
    }

    /**
     * Get EAV validator
     *
     * @return Backend
     */
    private function getEavValidator()
    {
        if ($this->eavValidator === null) {
            $this->eavValidator = ObjectManager::getInstance()->get(Backend::class);
        }
        return $this->eavValidator;
    }
}
