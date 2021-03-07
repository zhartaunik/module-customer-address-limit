<?php
declare(strict_types=1);

namespace PerfectCode\CustomerAddressLimit\Model;

use Magento\Customer\Model\ResourceModel\Address\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use PerfectCode\CustomerAddressLimit\Api\AddressRepositoryInterface;

class AddressRepository implements AddressRepositoryInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * AddressRepository constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(ScopeConfigInterface $scopeConfig, CollectionFactory $collectionFactory)
    {
        $this->scopeConfig = $scopeConfig;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @inheritDoc
     */
    public function canCreateNewAddress(int $customerId): bool
    {
        $addressNumber = $this->scopeConfig->getValue('customer/address/max_number', ScopeInterface::SCOPE_WEBSITE);
        if ($addressNumber) {
            $collection = $this->collectionFactory->create();
            $collection->setCustomerFilter([$customerId]);
            if ($collection->getSize() >= $addressNumber) {
                return false;
            }
        }

        return true;
    }
}
