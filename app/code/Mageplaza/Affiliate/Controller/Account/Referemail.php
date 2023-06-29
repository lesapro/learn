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

namespace Mageplaza\Affiliate\Controller\Account;

use Exception;
use Magento\Framework\DataObject;
use Mageplaza\Affiliate\Controller\Account;
use Zend_Validate;

/**
 * Class Referemail
 * @package Mageplaza\Affiliate\Controller\Account
 */
class Referemail extends Account
{
    const XML_PATH_REFER_EMAIL_TEMPLATE = 'affiliate/refer/account_sharing';

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $enable_refer = $this->dataHelper->isEnableReferFriend();
        if ($enable_refer) {
            $data = $this->getRequest()->getPostValue();
            $contacts = $data['contacts'];
            $subject = $data['subject'];
            $message = $data['content'];
            $sharingUrl = $data['refer_url'] ?: $this->dataHelper->getSharingUrl();
            $contacts = explode(',', $contacts);

            $successEmails = $errorEmails = [];

            foreach ($contacts as $key => $email) {
                if (strpos($email, '<') !== false) {
                    $name = substr($email, 0, strpos($email, '<'));
                    $email = substr($email, strpos($email, '<') + 1);
                } else {
                    $emailIdentify = explode('@', $email);
                    $name = $emailIdentify[0];
                }

                $name = trim($name, '\'"');
                $email = trim(rtrim(trim($email), '>'));
                try {
                    if (!Zend_Validate::is($email, 'EmailAddress')) {
                        continue;
                    }

                    $this->dataHelper->sendEmailTemplate(
                        new DataObject(['name' => $name, 'email' => $email, 'refer_url' => $sharingUrl]),
                        self::XML_PATH_REFER_EMAIL_TEMPLATE,
                        ['message' => $message, 'subject' => $subject]
                    );
                    $successEmails[] = $email;
                } catch (Exception $e) {
                    $errorEmails[] = $email;
                }
            }

            if ($successTotal = count($successEmails)) {
                $this->messageManager->addSuccessMessage(__(
                    'Total of %1 email(s) has been sent successfully.',
                    $successTotal
                ));
            }

            if (count($errorEmails)) {
                $this->messageManager->addErrorMessage(__(
                    'There is an error occurred while sending email to: %1. Please check and try again later.',
                    implode(', ', $errorEmails)
                ));
            }

            $this->_redirect('*/*/refer');
        } else {
            $this->messageManager->addNoticeMessage(__('Refer friends feature not available now. Please use this feature later.'));
            $this->_redirect('*/*/refer');
        }
    }
}
