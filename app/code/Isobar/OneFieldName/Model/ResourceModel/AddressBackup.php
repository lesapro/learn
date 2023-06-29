<?php

namespace Isobar\OneFieldName\Model\ResourceModel;

use Isobar\OneFieldName\Api\Data\AddressBackupInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class AddressBackup
 * @package Isobar\OneFieldName\Model\ResourceModel
 */
class AddressBackup extends AbstractDb
{
    /**
     * Initialize table and primary key name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            AddressBackupInterface::TABLE_NAME,
            AddressBackupInterface::ENTITY_ID
        );
    }

    /**
     * @param array $data
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function insertMultiple(array $data)
    {
        $connection = $this->getConnection();
        $table = $this->getMainTable();
        $fields = [
            AddressBackupInterface::ENTITY_ID,
            AddressBackupInterface::FIRSTNAME,
            AddressBackupInterface::LASTNAME
        ];

        $connection->beginTransaction();
        try {
            $connection->insertOnDuplicate($table, $data, $fields);
        } catch (\Exception $exception) {
            $connection->rollback();
            throw $exception;
        }
        $connection->commit();

        return $this;
    }
}
