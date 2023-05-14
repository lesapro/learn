<?php
/**
 * @category   Webkul
 * @package    Webkul_GoogleShoppingFeed
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\GoogleShoppingFeed\Model\Config\Source;

class OptionsForTax extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    /**
     * Return options array.
     *
     * @param int $store
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (null === $this->_options) {
            $this->_options = [
                ['value' => 'global', 'label' => __('Global')],
                ['value' => 'asproduct', 'label' => __('As Product')]
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
