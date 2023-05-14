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

namespace Mageplaza\Affiliate\Block\Account;

use Mageplaza\Affiliate\Block\Account;

/**
 * Class Home
 * @package Mageplaza\Affiliate\Block\Account
 */
class Home extends Account
{
    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('My Credit'));

        return parent::_prepareLayout();
    }

    /**
     * get account balance
     *
     * @return mixed
     */
    public function getAccountBalance()
    {
        return $this->getCurrentAccount()->getBalance();
    }

    /**
     * get account holding balance
     *
     * @return mixed
     */
    public function getAccountHoldingBalance()
    {
        return $this->getCurrentAccount()->getHoldingBalance();
    }

    /**
     * get account Total Commission
     *
     * @return mixed
     */
    public function getAccountTotalCommission()
    {
        return $this->getCurrentAccount()->getTotalCommission();
    }

    /**
     * get account Total Paid
     *
     * @return mixed
     */
    public function getAccountTotalPaid()
    {
        return $this->getCurrentAccount()->getTotalPaid();
    }
}
