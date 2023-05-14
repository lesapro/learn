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

namespace Mageplaza\Affiliate\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Rule\Model\AbstractModel;
use Magento\SalesRule\Model\Rule\Condition\CombineFactory;
use Magento\SalesRule\Model\Rule\Condition\Product\Combine;
use Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory as ConProdCombineFactory;

/**
 * Class Campaign
 * @package Mageplaza\Affiliate\Model
 * @method setCommission(array $unserialize)
 * @method getCommission()
 */
class Campaign extends AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'affiliate_campaign';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'affiliate_campaign';

    /**
     * @var string
     */
    protected $_eventPrefix = 'affiliate_campaign';

    /**
     * Store already validated addresses and validation results
     *
     * @var array
     */
    protected $_validatedAddresses = [];

    /**
     * @var CombineFactory
     */
    protected $_condCombineFactory;

    /**
     * @var ConProdCombineFactory
     */
    protected $_condProdCombineFactory;

    /**
     * Campaign constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param TimezoneInterface $localeDate
     * @param CombineFactory $condCombineFactory
     * @param ConProdCombineFactory $condProdCombineFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        TimezoneInterface $localeDate,
        CombineFactory $condCombineFactory,
        ConProdCombineFactory $condProdCombineFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_condCombineFactory = $condCombineFactory;
        $this->_condProdCombineFactory = $condProdCombineFactory;

        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mageplaza\Affiliate\Model\ResourceModel\Campaign');
    }

    /**
     * @return \Magento\SalesRule\Model\Rule\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->_condCombineFactory->create();
    }

    /**
     * Get rule condition product combine model instance
     *
     * @return Combine
     */
    public function getActionsInstance()
    {
        return $this->_condProdCombineFactory->create();
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
