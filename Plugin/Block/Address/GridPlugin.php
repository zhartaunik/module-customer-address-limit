<?php
declare(strict_types=1);
namespace PerfectCode\CustomerAddressLimit\Plugin\Block\Address;

use Magento\Customer\Block\Address\Grid;
use PerfectCode\CustomerAddressLimit\Api\AddressRepositoryInterface;

class GridPlugin
{
    /**
     * @var AddressRepositoryInterface
     */
    private AddressRepositoryInterface $addressRepository;

    /**
     * GridPlugin constructor.
     * @param AddressRepositoryInterface $addressRepository
     */
    public function __construct(AddressRepositoryInterface $addressRepository)
    {
        $this->addressRepository = $addressRepository;
    }

    /**
     * @param Grid $subject
     * @param string $result
     * @return string
     */
    public function afterToHtml(Grid $subject, string $result): string
    {
        if (!$this->addressRepository->canCreateNewAddress((int) $subject->getCustomer()->getId())) {
            $result = str_replace('role="add-address"', 'role="add-address" disabled="disabled"', $result);
            $result .= sprintf('<br/><p>%s</p>', __('You have reached the maximum number of addresses.'));
        }

        return $result;
    }
}
