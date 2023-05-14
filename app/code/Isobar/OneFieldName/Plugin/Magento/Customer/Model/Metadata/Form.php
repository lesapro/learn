<?php

namespace Isobar\OneFieldName\Plugin\Magento\Customer\Model\Metadata;

use Isobar\OneFieldName\Helper\Data as DataHelper;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\Data\AttributeMetadataInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Metadata\Form as BaseForm;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;

/**
 * Class Form
 * @package Isobar\OneFieldName\Plugin\Magento\Customer\Model\Metadata
 */
class Form
{
    /**
     * @var DataHelper
     */
    private $dataHelper;

    /**
     * Form constructor.
     *
     * @param DataHelper $dataHelper
     */
    public function __construct(
        DataHelper $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
    }

    /**
     * Plugin: Retrieve attributes metadata for the form
     *
     * @param BaseForm $subject
     * @param AttributeMetadataInterface[] $attributes
     *
     * @return AttributeMetadataInterface[]
     *
     * @throws NoSuchEntityException
     */
    public function afterGetAttributes(BaseForm $subject, array $attributes)
    {
        if (isset($attributes[AddressInterface::LASTNAME])
            && ($websiteId = $this->getWebsiteId())) {
            if ($this->dataHelper->isShowOneFieldName($websiteId)) {
                unset($attributes[AddressInterface::LASTNAME]);
            }
        }
        return $attributes;
    }

    /**
     * Plugin: Validate data array and return true or array of errors
     *
     * @param BaseForm $subject
     * @param boolean|array $result
     * @param array $data
     *
     * @return boolean|array
     */
    public function afterValidateData(BaseForm $subject, $result, array $data)
    {
        if (isset($data[AddressInterface::LASTNAME])
            && empty($data[AddressInterface::LASTNAME])
            && is_array($result)
        ) {
            try {
                $flag = false;
                $lastName = $subject->getAttribute(AddressInterface::LASTNAME);
                /** @var Phrase $error */
                foreach ($result as $key => $error) {
                    /** @var Phrase $argument */
                    foreach ($error->getArguments() as $argument) {
                        if (!empty($argument)
                            && $argument->getText() === $lastName->getStoreLabel()
                            && $this->dataHelper->isShowOneFieldName($this->getWebsiteId())
                        ) {
                            unset($result[$key]);
                            $flag = true;
                            break;
                        }
                    }
                    if ($flag === true) {
                        break;
                    }
                }
                if (empty($result)) {
                    return true;
                }
            } catch (\Exception $exception) {
                return $result;
            }
        }
        return $result;
    }

    /**
     * @return int
     *
     * @throws NoSuchEntityException
     */
    private function getWebsiteId()
    {
        $websiteId = $this->dataHelper->getWebsiteIdInQuoteSession();
        if (empty($websiteId)) {
            return $this->dataHelper->getByKey(CustomerInterface::WEBSITE_ID);
        }
        return $websiteId;
    }
}
