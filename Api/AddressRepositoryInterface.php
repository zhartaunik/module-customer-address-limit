<?php
declare(strict_types=1);

namespace PerfectCode\CustomerAddressLimit\Api;

/**
 * Custom address operations
 *
 * @api
 */
interface AddressRepositoryInterface
{
    /**
     * Verifies if customer can create new address.
     *
     * @param int $customerId
     * @return bool
     */
    public function canCreateNewAddress(int $customerId): bool;
}
