<?php
declare(strict_types=1);

namespace PerfectCode\CustomerAddressLimit\Test\Unit\Plugin\Model;

use Magento\Customer\Model\Address;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PerfectCode\CustomerAddressLimit\Api\AddressRepositoryInterface;
use PerfectCode\CustomerAddressLimit\Model\CanNotCreateException;
use PerfectCode\CustomerAddressLimit\Plugin\Model\AddressPlugin;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AddressPluginTest extends TestCase
{
    /**
     * @var AddressRepositoryInterface|MockObject
     */
    private AddressRepositoryInterface $addressRepository;

    /**
     * @var AddressPlugin|object
     */
    private object $plugin;

    /**
     * @inheridoc
     */
    protected function setUp(): void
    {
        $this->addressRepository = $this->getMockForAbstractClass(AddressRepositoryInterface::class);

        $objectManager = new ObjectManager($this);
        $this->plugin = $objectManager->getObject(
            AddressPlugin::class,
            [
                'addressRepository' => $this->addressRepository,
            ]
        );
    }

    /**
     * @return void
     * @throws CanNotCreateException
     */
    public function testBeforeBeforeSave(): void
    {
        $subject = $this->createMock(Address::class);
        $subject->expects($this->once())->method('isObjectNew')->willReturn(true);
        $subject->expects($this->once())->method('getCustomerId')->willReturn(234);

        $this->addressRepository->expects($this->once())->method('canCreateNewAddress')->willReturn(true);

        $this->plugin->beforeBeforeSave($subject);
        $this->doesNotPerformAssertions();
    }

    /**
     * @return void
     * @throws CanNotCreateException
     */
    public function testBeforeBeforeSaveException(): void
    {
        $this->expectException(CanNotCreateException::class);
        $this->expectExceptionMessage('Cannot create an address. The number of addresses is more than allowed.');

        $subject = $this->createMock(Address::class);
        $subject->expects($this->once())->method('isObjectNew')->willReturn(true);
        $subject->expects($this->once())->method('getCustomerId')->willReturn(234);

        $this->addressRepository->expects($this->once())->method('canCreateNewAddress')->willReturn(false);

        $this->plugin->beforeBeforeSave($subject);
    }
}
