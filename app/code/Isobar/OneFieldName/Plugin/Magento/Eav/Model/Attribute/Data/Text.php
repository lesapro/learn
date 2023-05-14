<?php

namespace Isobar\OneFieldName\Plugin\Magento\Eav\Model\Attribute\Data;

use Isobar\OneFieldName\Helper\Data as DataHelper;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Address;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Attribute\Data\Text as BaseText;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Text
 * @package Isobar\OneFieldName\Plugin\Magento\Eav\Model\Attribute\Data
 */
class Text
{
    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * Text constructor.
     *
     * @param DataHelper $dataHelper
     */
    public function __construct(
        DataHelper $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
    }

    /**
     * Plugin: Validate data
     *
     * Return true or array of errors
     *
     * @param BaseText $subject
     * @param bool|array $result
     * @param array|string $value
     *
     * @return bool|array
     */
    public function afterValidateValue(BaseText $subject, $result, $value)
    {
        if (empty($value) && is_array($result)) {
            try {
                $attribute = $subject->getAttribute();
                $websiteId = $this->getWebsiteId($subject->getEntity());
                if ($attribute->getAttributeCode() === CustomerInterface::LASTNAME
                    && $this->dataHelper->isShowOneFieldName($websiteId)
                ) {
                    return true;
                }
            } catch (\Exception $exception) {
                return $result;
            }
        }
        return $result;
    }

    /**
     * Get website id
     *
     * @param AbstractModel $model
     *
     * @return int|string|null
     * @throws NoSuchEntityException
     */
    private function getWebsiteId(AbstractModel $model)
    {
        if (($websiteId = $this->dataHelper->getByKey(CustomerInterface::WEBSITE_ID)) !== null) {
            return $websiteId;
        }

        if ($model instanceof Customer && !empty($customerWebsiteId = $model->getWebsiteId())) {
            return $customerWebsiteId;
        }

        if ($model instanceof Address) {
            return !empty($model->getStoreId())
                ? $this->dataHelper->getWebsiteIdByStoreId($model->getStoreId())
                : $this->dataHelper->getWebsiteIdInQuoteSession();
        }

        return null;
    }
}
