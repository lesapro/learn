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

namespace Mageplaza\Affiliate\Model\Transaction\Action\Withdraw;

use Magento\Framework\Phrase;
use Mageplaza\Affiliate\Model\Transaction\AbstractAction;
use Mageplaza\Affiliate\Model\Transaction\Type;

/**
 * Class Create
 * @package Mageplaza\Affiliate\Model\Transaction\Action\Withdraw
 */
class Create extends AbstractAction
{
    /**
     * @return int
     */
    public function getAmount()
    {
        return -(float)$this->getObject()->getAmount();
    }

    /**
     * @return int
     */
    public function getType()
    {
        return Type::PAID;
    }

    /**
     * @param null $transaction
     *
     * @return Phrase
     */
    public function getTitle($transaction = null)
    {
        return __('Subtract balance for withdraw request');
    }
}
