<?php

namespace Isobar\OneFieldName\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class MergeType
 * @package Isobar\OneFieldName\Model\Config\Source
 */
class MergeType implements OptionSourceInterface
{
    /**#@+
     * Define value
     */
    const MERGE_FIRST_LAST = 1;
    const MERGE_LAST_FIRST = 2;
    /**#@-*/

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::MERGE_FIRST_LAST,
                'label' => __('Merge firstname and lastname')
            ],
            [
                'value' => self::MERGE_LAST_FIRST,
                'label' => __('Merge lastname and firstname')
            ]
        ];
    }
}
