<?php

namespace Isobar\OneFieldName\Model;

use Isobar\OneFieldName\Api\CustomerManagementInterface;
use Isobar\OneFieldName\Api\Data\AddressBackupInterface;
use Isobar\OneFieldName\Api\Data\CustomerBackupInterface;
use Isobar\OneFieldName\Helper\Data as DataHelper;
use Isobar\OneFieldName\Model\ResourceModel\AddressBackup as AddressBackupResource;
use Isobar\OneFieldName\Model\ResourceModel\AddressBackup\Collection as AddressBackupCollection;
use Isobar\OneFieldName\Model\ResourceModel\AddressBackup\CollectionFactory as AddressBackupCollectionFactory;
use Isobar\OneFieldName\Model\ResourceModel\CustomerBackup as CustomerBackupResource;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\ResourceModel\Customer\Collection as CustomerCollection;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class CustomerManagement
 * @package Isobar\OneFieldName\Model
 */
class CustomerManagement implements CustomerManagementInterface
{
    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * @var AddressBackupResource
     */
    protected $addressBackupResource;

    /**
     * @var CustomerBackupResource
     */
    protected $customerBackupResource;

    /**
     * @var CustomerCollectionFactory
     */
    protected $customerCollectionFactory;

    /**
     * @var AddressBackupCollectionFactory
     */
    protected $addressBackupCollectionFactory;

    /**
     * CustomerManagement constructor.
     *
     * @param DataHelper $dataHelper
     * @param AddressBackupResource $addressBackupResource
     * @param CustomerBackupResource $customerBackupResource
     * @param CustomerCollectionFactory $customerCollectionFactory
     * @param AddressBackupCollectionFactory $addressBackupCollectionFactory
     */
    public function __construct(
        DataHelper $dataHelper,
        AddressBackupResource $addressBackupResource,
        CustomerBackupResource $customerBackupResource,
        CustomerCollectionFactory $customerCollectionFactory,
        AddressBackupCollectionFactory $addressBackupCollectionFactory
    ) {
        $this->dataHelper = $dataHelper;
        $this->addressBackupResource = $addressBackupResource;
        $this->customerBackupResource = $customerBackupResource;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->addressBackupCollectionFactory = $addressBackupCollectionFactory;
    }

    /**
     * @param array $websiteIds
     *
     * @return CustomerCollection
     *
     * @throws LocalizedException
     */
    private function getCustomerCollectionByWebsiteIds(array $websiteIds)
    {
        return $this->customerCollectionFactory->create()
            ->addAttributeToFilter('website_id', ['in' => $websiteIds]);
    }

    /**
     * @inheritDoc
     */
    public function mergeCustomerName(array $websiteIds)
    {
        try {
            /** @var CustomerCollection $customerCollection */
            $customerCollection = $this->getCustomerCollectionByWebsiteIds($websiteIds);

            /** @var CustomerCollection $customerBackupCollection */
            $customerBackupCollection = $this->getCustomerCollectionHasBackupData($websiteIds);

            if (empty($customerBackupCollection->getSize())) {
                $this->mergeAndBackupCustomerName($customerCollection);
            } else {
                $this->reUpdateFullName($customerBackupCollection);
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @inheritDoc
     */
    public function revertCustomerName(array $websiteIds)
    {
        try {
            $customerBackupIds = [];
            /** @var CustomerCollection $customerCollection */
            $customerCollection = $this->getCustomerCollectionHasBackupData($websiteIds);

            /** @var CustomerInterface $item */
            foreach ($customerCollection as $item) {
                if (!empty($item->getFirstnameBk()) && !empty($item->getLastnameBk())) {
                    $item->setFirstname($item->getFirstnameBk())
                        ->setLastname($item->getLastnameBk());
                    $customerBackupIds[] = $item->getId();
                }

                if (!empty($addresses = $item->getAddresses())) {
                    $addressIds = array_keys($addresses);
                    $addressBackupCollection = $this->getAddressBackupCollectionByAddressIds($addressIds);
                    $addressBackupArray = $this->convertAddressBackupToArray($addressBackupCollection);
                    /** @var AddressInterface $address */
                    foreach ($addresses as $address) {
                        $addressBackup = $addressBackupArray[$address->getId()];
                        $address->setFirstname($addressBackup[AddressBackupInterface::FIRSTNAME])
                            ->setLastname($addressBackup[AddressBackupInterface::LASTNAME]);
                    }
                    $addressBackupCollection->walk('delete');
                }
            }
            $customerCollection->save();
            if ($customerBackupIds) {
                $this->customerBackupResource->deleteMultiple($customerBackupIds);
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Get customer collection has backup data by website ids
     *
     * @param array $websiteIds
     *
     * @return CustomerCollection
     *
     * @throws LocalizedException
     */
    private function getCustomerCollectionHasBackupData(array $websiteIds)
    {
        /** @var CustomerCollection $collection */
        $collection = $this->getCustomerCollectionByWebsiteIds($websiteIds);
        $collection->getSelect()
            ->joinInner(
                ['icb' => CustomerBackupInterface::TABLE_NAME],
                'icb.entity_id = e.entity_id',
                [
                    'firstname_bk' => CustomerBackupInterface::FIRSTNAME,
                    'lastname_bk' => CustomerBackupInterface::LASTNAME
                ]
            );
        return $collection;
    }

    /**
     * @param array $addressIds
     *
     * @return AddressBackupCollection
     */
    private function getAddressBackupCollectionByAddressIds(array $addressIds)
    {
        return $this->addressBackupCollectionFactory->create()
            ->addFieldToFilter('entity_id', ['in' => $addressIds]);
    }

    /**
     * Convert address backup collection to array
     *
     * @param AddressBackupCollection $collection
     *
     * @return array
     */
    private function convertAddressBackupToArray(AddressBackupCollection $collection)
    {
        $result = [];
        /** @var AddressBackupInterface $item */
        foreach ($collection as $item) {
            $result[$item->getEntityId()] = [
                AddressBackupInterface::FIRSTNAME => $item->getFirstname(),
                AddressBackupInterface::LASTNAME => $item->getLastname()
            ];
        }
        return $result;
    }

    /**
     * Merge and backup customer's first name and last name
     *
     * @param CustomerCollection $customerCollection
     * @throws \Exception
     */
    protected function mergeAndBackupCustomerName(CustomerCollection $customerCollection)
    {
        $customerBackup = [];
        $addressBackup = [];
        /** @var CustomerInterface $item */
        foreach ($customerCollection as $item) {
            $websiteId = $item->getWebsiteId();
            if (!empty($item->getLastname())) {
                $customerBackup[] = [
                    CustomerBackupInterface::ENTITY_ID => $item->getId(),
                    CustomerBackupInterface::FIRSTNAME => $item->getFirstname(),
                    CustomerBackupInterface::LASTNAME => $item->getLastname()
                ];
                $customerFullname = $this->dataHelper->mergeTwoFieldName(
                    $item->getFirstname(),
                    $item->getLastname(),
                    $websiteId
                );
                $item->setFirstname($customerFullname)
                    ->setLastname(null);
            }
            if (!empty($item->getAddresses())) {
                /** @var AddressInterface $address */
                foreach ($item->getAddresses() as $address) {
                    if (!empty($address->getLastname())) {
                        $addressBackup[] = [
                            AddressBackupInterface::ENTITY_ID => $address->getId(),
                            AddressBackupInterface::FIRSTNAME => $address->getFirstname(),
                            AddressBackupInterface::LASTNAME => $address->getLastname()
                        ];
                        $addressFullname = $this->dataHelper->mergeTwoFieldName(
                            $address->getFirstname(),
                            $address->getLastname(),
                            $websiteId
                        );
                        $address->setFirstname($addressFullname)
                            ->setLastname(null);
                    }
                }
            }
        }
        $customerCollection->save();
        if ($customerBackup) {
            $this->customerBackupResource->insertMultiple($customerBackup);
        }
        if ($addressBackup) {
            $this->addressBackupResource->insertMultiple($addressBackup);
        }
    }

    /**
     * Re-update customer and address customer full name
     *
     * @param CustomerCollection $customerCollection
     */
    protected function reUpdateFullName(CustomerCollection $customerCollection)
    {
        /** @var CustomerInterface $item */
        foreach ($customerCollection as $item) {
            $websiteId = $item->getWebsiteId();
            $customerFullname = $this->dataHelper->mergeTwoFieldName(
                $item->getFirstnameBk(),
                $item->getLastnameBk(),
                $websiteId
            );
            $item->setFirstname($customerFullname);

            if (!empty($addresses = $item->getAddresses())) {
                $addressIds = array_keys($addresses);
                $addressBackupCollection = $this->getAddressBackupCollectionByAddressIds($addressIds);
                $addressBackupArray = $this->convertAddressBackupToArray($addressBackupCollection);

                /** @var AddressInterface $address */
                foreach ($item->getAddresses() as $address) {
                    $addressBackup = $addressBackupArray[$address->getId()];
                    $addressFullname = $this->dataHelper->mergeTwoFieldName(
                        $addressBackup[AddressBackupInterface::FIRSTNAME],
                        $addressBackup[AddressBackupInterface::LASTNAME],
                        $websiteId
                    );
                    $address->setFirstname($addressFullname);
                }
            }
        }
        $customerCollection->save();
    }
}
