<?php
declare(strict_types=1);

namespace PerfectCode\CustomerAddressLimit\Test\Api\AddressRepository;

use Magento\Framework\Registry;
use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Helper\Customer;
use Magento\TestFramework\Helper\Customer as CustomerHelper;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Integration\Api\CustomerTokenServiceInterface;
use PerfectCode\CustomerAddressLimit\Model\CanNotCreateException;

class CanCreateNewAddressTest extends WebapiAbstract
{
    /**
     * Webapi Endpoint
     */
    const RESOURCE_PATH = '/V1/customers/me/addresses/canCreate';

    /**
     * @var int[]
     */
    private $currentCustomerId = [];

    /**
     * @var CustomerRepositoryInterface|null
     */
    private $customerRepository;

    /**
     * @var CustomerHelper
     */
    private $customerHelper;

    /**
     * @var CustomerTokenServiceInterface
     */
    private $customerTokenService;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->customerRepository = Bootstrap::getObjectManager()->get(CustomerRepositoryInterface::class);
        $this->customerHelper = Bootstrap::getObjectManager()->get(CustomerHelper::class);
        $this->customerTokenService = Bootstrap::getObjectManager()->get(CustomerTokenServiceInterface::class);
    }

    /**
     * Ensure that fixture customer and his addresses are deleted.
     */
    protected function tearDown(): void
    {
        $this->customerRepository = null;

        /** @var Registry $registry */
        $registry = Bootstrap::getObjectManager()->get(Registry::class);
        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', true);

        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', false);
        parent::tearDown();
    }

    /**
     * @magentoApiDataFixture Magento/Customer/_files/customer.php
     * @magentoApiDataFixture Magento/Customer/_files/customer_two_addresses.php
     * @magentoConfigFixture customer/address/max_number 5
     */
    public function testCanCreateNewAddressSuccess()
    {
        $token = $this->customerTokenService->createCustomerAccessToken(
            'customer@example.com',
            'password'
        );

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => Request::HTTP_METHOD_GET,
                'token' => $token,
            ],
        ];

        $this->assertTrue($this->_webApiCall($serviceInfo, ['customerId' => 1]));
    }

    /**
     * @magentoApiDataFixture Magento/Customer/_files/customer.php
     * @magentoApiDataFixture Magento/Customer/_files/customer_two_addresses.php
     * @magentoConfigFixture customer/address/max_number 0
     */
    public function testCanCreateNewAddressConfigZeroSuccess()
    {
        $token = $this->customerTokenService->createCustomerAccessToken(
            'customer@example.com',
            'password'
        );

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => Request::HTTP_METHOD_GET,
                'token' => $token,
            ],
        ];

        $this->assertTrue($this->_webApiCall($serviceInfo, ['customerId' => 1]));
    }

    /**
     * @magentoApiDataFixture Magento/Customer/_files/customer.php
     * @magentoApiDataFixture Magento/Customer/_files/customer_two_addresses.php
     * @magentoConfigFixture customer/address/max_number 2
     */
    public function testCanCreateNewAddressCannotCreateException()
    {
        $token = $this->customerTokenService->createCustomerAccessToken(
            'customer@example.com',
            'password'
        );

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => Request::HTTP_METHOD_GET,
                'token' => $token,
            ],
        ];

        $this->assertFalse($this->_webApiCall($serviceInfo, ['customerId' => 1]));
    }
}
