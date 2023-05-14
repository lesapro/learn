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
 * Class Signup
 * @package Mageplaza\Affiliate\Block\Account
 */
class Signup extends Account
{
    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Signup Affiliate'));

        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getSignUpUrl()
    {
        return $this->getUrl('affiliate/account/signuppost');
    }
}
