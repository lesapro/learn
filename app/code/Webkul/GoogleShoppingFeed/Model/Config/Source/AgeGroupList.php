<?php
/**
 * @category   Webkul
 * @package    Webkul_GoogleShoppingFeed
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\GoogleShoppingFeed\Model\Config\Source;

class AgeGroupList extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    /**
     * Return options array.
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (null === $this->_options) {
            $this->_options = [
                ['value' => '', 'label' => __('Select')],
                ['value' => 'adult', 'label' => __('Adult')],
                ['value' => 'infant', 'label' => __('Infant')],
                ['value' => 'kids', 'label' => __('Kids')],
                ['value' => 'newborn', 'label' => __('Newborn')],
                ['value' => 'toddler', 'label' => __('Toddler')]
            ];
        }

        return $this->_options;
    }

    /**
     * Get options in "key-value" format.
     *
     * @param string $value
     * @return array
     */
    public function getOptionText($value)
    {
        foreach ($this->getAllOptions() as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }
}
