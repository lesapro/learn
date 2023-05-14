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

namespace Mageplaza\Affiliate\Model\Total\Order\Invoice;

use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;
use Mageplaza\Affiliate\Helper\Calculation;

/**
 * Class Affiliate
 * @package Mageplaza\Affiliate\Model\Total\Order\Invoice
 */
class Affiliate extends AbstractTotal
{
    /**
     * @var Calculation
     */
    protected $calculation;

    /**
     * Affiliate constructor.
     *
     * @param Calculation $calculation
     * @param array $data
     */
    public function __construct(
        Calculation $calculation,
        array $data = []
    ) {
        $this->calculation = $calculation;

        parent::__construct($data);
    }

    /**
     * @param Invoice $invoice
     *
     * @return $this
     */
    public function collect(Invoice $invoice)
    {
        $this->calculation->calculateAffiliateDiscount(
            $invoice,
            ['affiliate_discount_invoiced', 'base_affiliate_discount_invoiced']
        );

        return $this;
    }
}
