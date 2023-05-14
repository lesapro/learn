<?php

namespace Isobar\OneFieldName\Plugin\Magento\Customer\Model\Address\Validator;

use Isobar\OneFieldName\Helper\Data as DataHelper;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Model\Address\AbstractAddress;
use Magento\Customer\Model\Address\Validator\General as BaseGeneral;
use Magento\Framework\Phrase;

/**
 * Class General
 * @package Isobar\OneFieldName\Plugin\Magento\Customer\Model\Address\Validator
 */
class General
{
    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * General constructor.
     *
     * @param DataHelper $dataHelper
     */
    public function __construct(
        DataHelper $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
    }

    /**
     * @param BaseGeneral $subject
     * @param array $errors
     * @param AbstractAddress $address
     *
     * @return array
     */
    public function afterValidate(BaseGeneral $subject, array $errors, AbstractAddress $address)
    {
        $websiteId = !empty($address->getStoreId())
            ? $this->dataHelper->getWebsiteIdByStoreId($address->getStoreId())
            : null;
        /** @var Phrase $error */
        foreach ($errors as $key => $error) {
            if (empty($address->getLastname())
                && $this->dataHelper->isShowOneFieldName($websiteId)
                && isset($error->getArguments()['fieldName'])
                && $error->getArguments()['fieldName'] === AddressInterface::LASTNAME
            ) {
                unset($errors[$key]);
                break;
            }
        }
        return $errors;
    }
}
