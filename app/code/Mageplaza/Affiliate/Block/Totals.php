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

namespace Mageplaza\Affiliate\Block;

use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;

/**
 * Class Totals
 * @package Mageplaza\Affiliate\Block
 */
class Totals extends Template
{
    /**
     * @return $this
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $source = $parent->getSource();

        if ($source->getAffiliateDiscountAmount() != 0) {
            $parent->addTotal(new DataObject([
                'code'       => 'affiliate_discount',
                'value'      => -$source->getAffiliateDiscountAmount(),
                'base_value' => -$source->getBaseAffiliateDiscountAmount(),
                'label'      => __('Affiliate Discount')
            ]));
        }

        return $this;
    }
}
