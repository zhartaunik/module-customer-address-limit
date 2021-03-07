<?php
declare(strict_types=1);

namespace PerfectCode\CustomerAddressLimit\Test\Unit\Plugin\Block\Address;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Block\Address\Grid;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PerfectCode\CustomerAddressLimit\Api\AddressRepositoryInterface;
use PerfectCode\CustomerAddressLimit\Plugin\Block\Address\GridPlugin;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GridPluginTest extends TestCase
{
    /**
     * @var AddressRepositoryInterface|MockObject
     */
    private AddressRepositoryInterface $addressRepository;

    /**
     * @var GridPlugin|object
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
            GridPlugin::class,
            [
                'addressRepository' => $this->addressRepository,
            ]
        );
    }

    /**
     * @dataProvider afterHtmlDataProvider
     * @param string $expected
     * @param bool $canCreate
     */
    public function testAfterToHtml(string $expected, bool $canCreate): void
    {
        $content = <<<HTML
<div class="block block-addresses-list">
    <div class="block-title"><strong>Additional Address Entries</strong></div>
</div>
<div class="actions-toolbar">
    <div class="primary">
        <button type="button" role="add-address" title="Add&#x20;New&#x20;Address" class="action primary add"><span>Add New Address</span></button>
    </div>
    <div class="secondary">
        <a class="action back" href="https://magento2.docker/customer/account/"><span>Back</span></a>
    </div>
</div>
HTML;
        $customer = $this->getMockForAbstractClass(CustomerInterface::class);
        $customer->expects($this->once())->method('getId')->willReturn('345');

        $subject = $this->createMock(Grid::class);
        $subject->expects($this->once())->method('getCustomer')->willReturn($customer);

        $this->addressRepository->expects($this->once())->method('canCreateNewAddress')->willReturn($canCreate);

        $this->assertEquals($expected, $this->plugin->afterToHtml($subject, $content));
    }

    public function afterHtmlDataProvider()
    {
        return [
            [
                'expected' => <<<HTML
<div class="block block-addresses-list">
    <div class="block-title"><strong>Additional Address Entries</strong></div>
</div>
<div class="actions-toolbar">
    <div class="primary">
        <button type="button" role="add-address" title="Add&#x20;New&#x20;Address" class="action primary add"><span>Add New Address</span></button>
    </div>
    <div class="secondary">
        <a class="action back" href="https://magento2.docker/customer/account/"><span>Back</span></a>
    </div>
</div>
HTML,
                'canCreate' => true,
            ],
            [
                'expected' => <<<HTML
<div class="block block-addresses-list">
    <div class="block-title"><strong>Additional Address Entries</strong></div>
</div>
<div class="actions-toolbar">
    <div class="primary">
        <button type="button" role="add-address" disabled="disabled" title="Add&#x20;New&#x20;Address" class="action primary add"><span>Add New Address</span></button>
    </div>
    <div class="secondary">
        <a class="action back" href="https://magento2.docker/customer/account/"><span>Back</span></a>
    </div>
</div><br/><p>You have reached the maximum number of addresses.</p>
HTML,
                'canCreate' => false,
            ],
        ];
    }
}
