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

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Actions
 * @package Mageplaza\Affiliate\Ui\Component\Listing\Column
 */
class Actions extends Column
{
    /**
     * URL builder
     *
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * Actions constructor.
     *
     * @param UrlInterface $urlBuilder
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        UrlInterface $urlBuilder,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->_urlBuilder = $urlBuilder;

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
            $config = $this->getData('config');
            foreach ($dataSource['data']['items'] as & $item) {
                $indexField = $config['indexField'];
                if (isset($item[$indexField])) {
                    foreach ($config['action_list'] as $name => $action) {
                        $actionArray = [
                            'href' => $this->_urlBuilder->getUrl(
                                $action['url_path'],
                                [$config['paramName'] => $item[$indexField]]
                            ),
                            'label' => __($action['label'])
                        ];
                        if ($name == 'delete') {
                            $actionArray['confirm'] = [
                                'title' => __('Delete "%1"', $item[$indexField]),
                                'message' => __('Are you sure?')
                            ];
                        }
                        $item[$this->getData('name')][$name] = $actionArray;
                    }
                }
            }
        }

        return $dataSource;
    }
}
