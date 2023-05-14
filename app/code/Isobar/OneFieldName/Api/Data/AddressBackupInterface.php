<?php

namespace Isobar\OneFieldName\Api\Data;

/**
 * Interface AddressBackupInterface
 * @package Isobar\OneFieldName\Api\Data
 */
interface AddressBackupInterface
{
    /**
     * @var string
     */
    const TABLE_NAME = 'isobar_customer_address_backup';

    /**#@+
     * Constants defined for keys of data array
     */
    const ENTITY_ID = 'entity_id';
    const FIRSTNAME = 'firstname';
    const LASTNAME = 'lastname';
    /**#@-*/

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId);

    /**
     * @return string
     */
    public function getFirstname();

    /**
     * @param string $firstname
     * @return $this
     */
    public function setFirstname($firstname);

    /**
     * @return string
     */
    public function getLastname();

    /**
     * @param string $lastname
     * @return $this
     */
    public function setLastname($lastname);
}
