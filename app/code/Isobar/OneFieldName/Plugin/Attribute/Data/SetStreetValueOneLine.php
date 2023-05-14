<?php

namespace Isobar\OneFieldName\Plugin\Attribute\Data;

class SetStreetValueOneLine
{
    public function beforeValidateValue($subject, $value) {
        /**
         * @var \Magento\Eav\Model\Attribute $attribute
         */
        $attribute = $subject->getAttribute();
        if ($attribute->getAttributeCode() == 'street' && !empty($value)) {
            $entity = $subject->getEntity();
            $values = $entity->getDataUsingMethod($attribute->getAttributeCode());
            if (!is_array($values)) {
                $values = explode("\n", $values);
            }
            return [$values[0]];
        }
        return [$value];
    }
}
