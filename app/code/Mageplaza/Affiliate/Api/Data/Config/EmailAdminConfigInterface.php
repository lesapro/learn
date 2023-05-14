<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
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

namespace Mageplaza\Affiliate\Api\Data\Config;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface EmailAdminConfigInterface
 * @api
 */
interface EmailAdminConfigInterface extends ExtensibleDataInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const EMAILS_TO = 'emails_to';

    const ENABLE_SIGN_UP = 'enable_sign_up';

    const SIGN_UP_TEMPLATE = 'sign_up_template';

    const ENABLE_WITHDRAW_REQUEST = 'enable_withdraw_request';

    const WITHDRAW_REQUEST_TEMPLATE = 'withdraw_request_template';

    /**
     * @return string
     */
    public function getEmailsTo();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setEmailsTo($value);

    /**
     * @return bool
     */
    public function getEnableSignUp();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setEnableSignUp($value);

    /**
     * @return string
     */
    public function getSignUpTemplate();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setSignUpTemplate($value);

    /**
     * @return bool
     */
    public function getEnableWithdrawRequest();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setEnableWithdrawRequest($value);

    /**
     * @return string
     */
    public function getWithdrawRequestTemplate();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setWithdrawRequestTemplate($value);
}
