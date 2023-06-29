<?php

namespace Isobar\OneFieldName\Model\ResourceModel;

use Isobar\OneFieldName\Api\Data\CustomerBackupInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class CustomerBackup
 * @package Isobar\OneFieldName\Model\ResourceModel
 */
class CustomerBackup extends AbstractDb
{
    /**
     * Initialize table and primary key name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            CustomerBackupInterface::TABLE_NAME,
            CustomerBackupInterface::ENTITY_ID
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
            CustomerBackupInterface::ENTITY_ID,
            CustomerBackupInterface::FIRSTNAME,
            CustomerBackupInterface::LASTNAME
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

    /**
     * @param array $entityIds
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function deleteMultiple(array $entityIds)
    {
        $connection = $this->getConnection();
        $table = $this->getMainTable();

        $connection->beginTransaction();
        try {
            $condition = ['entity_id IN (?)' => $entityIds];
            $connection->delete($table, $condition);
        } catch (\Exception $exception) {
            $connection->rollback();
            throw $exception;
        }
        $connection->commit();

        return $this;
    }
}
