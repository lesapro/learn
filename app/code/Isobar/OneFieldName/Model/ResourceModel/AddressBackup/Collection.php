<?php

namespace Isobar\OneFieldName\Model\ResourceModel\AddressBackup;

use Isobar\OneFieldName\Model\AddressBackup;
use Isobar\OneFieldName\Model\ResourceModel\AddressBackup as AddressBackupResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Isobar\OneFieldName\Model\ResourceModel\AddressBackup
 */
class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            AddressBackup::class,
            AddressBackupResource::class
        );
    }
}
