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

namespace Mageplaza\Affiliate\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Mageplaza\Affiliate\Model\CampaignFactory;

/**
 * Class Campaign
 * @package Mageplaza\Affiliate\Ui\Component\Listing\Column
 */
class Campaign extends Column
{
    /**
     * @var CampaignFactory
     */
    protected $campaignFactory;

    /**
     * Campaign constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param CampaignFactory $campaignFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        CampaignFactory $campaignFactory,
        array $components = [],
        array $data = []
    ) {
        $this->campaignFactory = $campaignFactory->create();

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as $item) {
                $campaignName = [];
                $campaignIds = null;
                if (isset($item['campaign_id']) && $item['campaign_id'] !== null) {
                    $campaignIds = explode(',', $item['campaign_id']);
                }
                if ($campaignIds !== null) {
                    foreach ($campaignIds as $campaignId) {
                        $campaignName[] = $this->campaignFactory->load($campaignId)->getName();
                    }
                    $item[$this->getData('name')] = implode(', ', $campaignName);
                } else {
                    $item[$this->getData('name')] = null;
                }
            }
        }

        return $dataSource;
    }
}
