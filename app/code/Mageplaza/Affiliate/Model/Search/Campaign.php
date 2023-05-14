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

namespace Mageplaza\Affiliate\Model\Search;

use Magento\Backend\Helper\Data;
use Magento\Framework\DataObject;
use Mageplaza\Affiliate\Model\ResourceModel\Campaign\CollectionFactory;

/**
 * Class Campaign
 * @package Mageplaza\Affiliate\Model\Search
 */
class Campaign extends DataObject
{
    /**
     * Campaign Collection factory
     *
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * Backend data helper
     *
     * @var Data
     */
    protected $_adminhtmlData;

    /**
     * Campaign constructor.
     *
     * @param CollectionFactory $collectionFactory
     * @param Data $adminhtmlData
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        Data $adminhtmlData
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_adminhtmlData = $adminhtmlData;

        parent::__construct();
    }

    /**
     * Load search results
     *
     * @return $this
     */
    public function load()
    {
        $result = [];
        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($result);

            return $this;
        }

        $query = $this->getQuery();
        $collection = $this->_collectionFactory->create()
            ->addFieldToFilter('name', ['like' => '%' . $query . '%'])
            ->setCurPage($this->getStart())
            ->setPageSize($this->getLimit())
            ->load();

        foreach ($collection as $campaign) {
            $result[] = [
                'id' => 'mageplaza_affiliate_campaign/1/' . $campaign->getId(),
                'type' => __('Affiliate Campaign'),
                'name' => $campaign->getName(),
                'description' => $campaign->getDescription(),
                'form_panel_title' => __(
                    'Campaign %1',
                    $campaign->getName()
                ),
                'url' => $this->_adminhtmlData->getUrl(
                    'mageplaza_affiliate/campaign/edit',
                    ['campaign_id' => $campaign->getId()]
                ),
            ];
        }

        $this->setResults($result);

        return $this;
    }
}
