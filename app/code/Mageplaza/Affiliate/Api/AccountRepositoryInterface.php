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

namespace Mageplaza\Affiliate\Api;

/**
 * Interface AccountRepositoryInterface
 * @api
 */
interface AccountRepositoryInterface
{
    /**
     * @param int|null $storeId
     * @return \Mageplaza\Affiliate\Api\Data\AccountInterface Account.
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($storeId = null);

    /**
     * @param bool $isSubscribe
     * @param int|null $storeId
     * @return bool false for unsubscribed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function subscribe($isSubscribe, $storeId = null);

    /**
     * @param string $contacts
     * @param string|null $referUrl
     * @param string|null $subject
     * @param string|null $content
     * @param int|null $storeId
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function invite($contacts, $referUrl = null, $subject = null, $content = null, $storeId = null);

    /**
     * @param string|null $email
     * @param int|null $storeId
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function signup($email = null, $storeId = null);

    /**
     * @param string $url
     * @param int|null $storeId
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Exception
     */
    public function createReferLink($url, $storeId = null);

    /**
     * Lists transaction that match specified search criteria.
     *
     * This call returns an array of objects, but detailed information about each object’s attributes might not be
     * included.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria The search criteria.
     * @param int|null $storeId
     *
     * @return \Mageplaza\Affiliate\Api\Data\TransactionSearchResultInterface Transaction search result
     *     interface.
     */
    public function transactions(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria, $storeId = null);

    /**
     * Lists withdraw that match specified search criteria.
     *
     * This call returns an array of objects, but detailed information about each object’s attributes might not be
     * included.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria The search criteria.
     * @param int|null $storeId
     *
     * @return \Mageplaza\Affiliate\Api\Data\WithdrawSearchResultInterface Withdraw search result
     *     interface.
     */
    public function withdrawsHistory(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria, $storeId = null);

    /**
     * Required(account_id, amount, payment_method)
     * Paypal method required paypal_email field
     *
     * @param \Mageplaza\Affiliate\Api\Data\WithdrawInterface $data
     * @param int|null $storeId
     *
     * @return int Withdraw id created
     * @throws \Magento\Framework\Exception\LocalizedException;
     */
    public function withdraw(\Mageplaza\Affiliate\Api\Data\WithdrawInterface $data, $storeId = null);

    /**
     * Lists campaign that match customer.
     *
     * @param int|null $storeId
     *
     * @return \Mageplaza\Affiliate\Api\Data\CampaignSearchResultInterface Withdraw search result
     */
    public function campaigns($storeId = null);

    /**
     * Lists campaign that match guest.
     *
     * @param int|null $storeId
     *
     * @return \Mageplaza\Affiliate\Api\Data\CampaignSearchResultInterface Withdraw search result
     */
    public function guestCampaigns($storeId = null);
}
