<?php

namespace Isobar\OneFieldName\Model;

use Isobar\OneFieldName\Api\Data\CustomerBackupInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class CustomerBackup
 * @package Isobar\OneFieldName\Model
 */
class CustomerBackup extends AbstractModel implements CustomerBackupInterface
{
    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\CustomerBackup::class);
    }

    /**
     * @inheritdoc
     */
    public function getEntityId()
    {
        return $this->_getData(self::ENTITY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * @inheritDoc
     */
    public function getFirstname()
    {
        return $this->_getData(self::FIRSTNAME);
    }

    /**
     * @inheritDoc
     */
    public function setFirstname($firstname)
    {
        return $this->setData(self::FIRSTNAME, $firstname);
    }

    /**
     * @inheritDoc
     */
    public function getLastname()
    {
        return $this->_getData(self::LASTNAME);
    }

    /**
     * @inheritDoc
     */
    public function setLastname($lastname)
    {
        return $this->setData(self::LASTNAME, $lastname);
    }
}
