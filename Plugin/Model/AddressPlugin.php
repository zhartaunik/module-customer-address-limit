<?php
declare(strict_types=1);

namespace PerfectCode\CustomerAddressLimit\Plugin\Model;

use PerfectCode\CustomerAddressLimit\Api\AddressRepositoryInterface;
use PerfectCode\CustomerAddressLimit\Model\CanNotCreateException;
use Magento\Customer\Model\Address;

/**
 * Class AddressPlugin
 *
 * On before save action verifies if number of existing addresses does not exceed the allowed.
 */
class AddressPlugin
{
    /**
     * @var AddressRepositoryInterface
     */
    private AddressRepositoryInterface $addressRepository;

    /**
     * AddressPlugin constructor.
     * @param AddressRepositoryInterface $addressRepository
     */
    public function __construct(AddressRepositoryInterface $addressRepository)
    {
        $this->addressRepository = $addressRepository;
    }

    /**
     * @param Address $subject
     * @return void
     * @throws CanNotCreateException
     */
    public function beforeBeforeSave(Address $subject): void
    {
        if ($subject->isObjectNew()
            && !$this->addressRepository->canCreateNewAddress((int) $subject->getCustomerId())
        ) {
            throw new CanNotCreateException(
                __('Cannot create an address. The number of addresses is more than allowed.')
            );
        }
    }
}
