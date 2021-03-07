<?php
declare(strict_types=1);

namespace PerfectCode\CustomerAddressLimit\Test\Unit\Model;

use Magento\Customer\Model\ResourceModel\Address\Collection;
use Magento\Customer\Model\ResourceModel\Address\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Model\ScopeInterface;
use PerfectCode\CustomerAddressLimit\Model\AddressRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Rule\InvokedCount as InvokedCountMatcher;
use PHPUnit\Framework\TestCase;

class AddressRepositoryTest extends TestCase
{
    /**
     * @var ScopeConfigInterface|MockObject
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @var CollectionFactory|MockObject
     */
    private CollectionFactory $collectionFactory;

    /**
     * @var AddressRepository|object
     */
    private object $model;

    /**
     * @inheridoc
     */
    protected function setUp(): void
    {
        $this->scopeConfig = $this->getMockForAbstractClass(ScopeConfigInterface::class);
        $this->collectionFactory = $this->createMock(CollectionFactory::class);

        $objectManager = new ObjectManager($this);
        $this->model = $objectManager->getObject(
            AddressRepository::class,
            [
                'scopeConfig' => $this->scopeConfig,
                'collectionFactory' => $this->collectionFactory,
            ]
        );
    }

    /**
     * @dataProvider createAddressDataProvider
     * @param int $calls
     * @param int $collSize
     * @param int $configLimit
     * @param bool $result
     * @return void
     */
    public function testCanCreateNewAddress(
        int $calls,
        int $collSize,
        int $configLimit,
        bool $result
    ): void {
        $this->scopeConfig->expects($this->once())->method('getValue')->with(
            'customer/address/max_number',
            ScopeInterface::SCOPE_WEBSITE
        )->willReturn($configLimit);

        $collection = $this->createMock(Collection::class);
        $collection->expects($this->exactly($calls))->method('setCustomerFilter')->with([234])->willReturnSelf();
        $collection->expects($this->exactly($calls))->method('getSize')->willReturn($collSize);

        $this->collectionFactory->expects($this->exactly($calls))->method('create')->willReturn($collection);

        $this->assertEquals($result, $this->model->canCreateNewAddress(234));
    }

    /**
     * @return array[]
     */
    public function createAddressDataProvider(): array
    {
        return [
            [
                'calls' => 1,
                'collSize' => 3,
                'configLimit' => 5,
                'result' => true,
            ],
            [
                'calls' => 1,
                'collSize' => 5,
                'configLimit' => 5,
                'result' => false,
            ],
            [
                'calls' => 0,
                'collSize' => 555,
                'configLimit' => 0,
                'result' => true,
            ],
        ];
    }
}
