<?php

namespace Isobar\OneFieldName\Ui\Component\Listing\Address\Column;

class Street extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                if (isset($item['entity_id'])) {
                    $streets= explode(PHP_EOL, $item['street']);
                    $item[$name] = $streets[0];
                }
            }
        }

        return $dataSource;
    }
}
