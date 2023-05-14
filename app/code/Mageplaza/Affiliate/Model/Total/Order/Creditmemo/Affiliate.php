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

namespace Mageplaza\Affiliate\Model\Total\Order\Creditmemo;

use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;
use Mageplaza\Affiliate\Helper\Calculation;

/**
 * Class Affiliate
 * @package Mageplaza\Affiliate\Model\Total\Order\Creditmemo
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
     * @param Creditmemo $creditmemo
     *
     * @return $this
     */
    public function collect(Creditmemo $creditmemo)
    {
        $this->calculation->calculateAffiliateDiscount(
            $creditmemo,
            ['affiliate_discount_refunded', 'base_affiliate_discount_refunded']
        );

        return $this;
    }
}
