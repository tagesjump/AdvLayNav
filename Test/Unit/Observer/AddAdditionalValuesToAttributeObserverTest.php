<?php
/**
 * Copyright © PART <info@part-online.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Part\AdvLayNav\Test\Unit\Observer;

/**
 * Class AddAdditionalValuesToAttributeObserverTest
 */
class AddAdditionalValuesToAttributeObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Module\Manager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $moduleManagerMock;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute|\PHPUnit_Framework_MockObject_MockObject
     */
    private $attributeMock;

    /**
     * @var \Magento\Framework\Event\Observer|\PHPUnit_Framework_MockObject_MockObject
     */
    private $eventObserverMock;

    /**
     * @var \Part\AdvLayNav\Observer\AddFieldsToAttributeObserver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $observer;

    public function setUp()
    {
        $this->moduleManagerMock = $this->getMock(
            '\Magento\Framework\Module\Manager',
            [],
            [],
            '',
            false
        );
        $this->eventObserverMock = $this->getMock(
            '\Magento\Framework\Event\Observer',
            ['getEvent', 'getAttribute'],
            [],
            '',
            false
        );
        $this->attributeMock = $this->getMock(
            '\Magento\Catalog\Model\ResourceModel\Eav\Attribute',
            ['getData', 'setData'],
            [],
            '',
            false
        );
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->observer = $objectManager->getObject(
            'Part\AdvLayNav\Observer\AddAdditionalValuesToAttributeObserver',
            [
                'moduleManager' => $this->moduleManagerMock,
            ]
        );
    }

    /**
     * @dataProvider dataAddAdditionalValues
     */
    public function testAddAdditionalValues($isOutputEnabled, $getAttributeCount, $getDataCount, $data, $setDataCount)
    {
        $this->moduleManagerMock
            ->expects($this->once())
            ->method('isOutputEnabled')
            ->willReturn($isOutputEnabled);
        $this->eventObserverMock
            ->expects($this->exactly($getAttributeCount))
            ->method('getAttribute')
            ->willReturn($this->attributeMock);
        $this->attributeMock
            ->expects($this->exactly($getDataCount))
            ->method('getData')
            ->willReturn($data);
        $this->attributeMock
            ->expects($this->exactly($setDataCount))
            ->method('setData');

        $this->observer->execute($this->eventObserverMock);
    }

    /**
     * @return array
     */
    public function dataAddAdditionalValues()
    {
        return [
            [
                false,
                0,
                0,
                [],
                0,
            ],
            [
                true,
                1,
                1,
                [],
                0,
            ],
            [
                true,
                1,
                1,
                ['additional_data' => serialize(['advlaynav_input_type' => 'none'])],
                1,
            ],
        ];
    }
}
