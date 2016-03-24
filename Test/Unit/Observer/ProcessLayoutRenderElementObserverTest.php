<?php
/**
 * Copyright © PART <info@part-online.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Part\AdvLayNav\Test\Unit\Observer;

/**
 * Class ProcessLayoutRenderElementObserverTest
 */
class ProcessLayoutRenderElementObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Event\Observer|\PHPUnit_Framework_MockObject_MockObject
     */
    private $eventObserverMock;

    /**
     * @var \Magento\Framework\DataObject|\PHPUnit_Framework_MockObject_MockObject
     */
    private $transportMock;

    /**
     * @var \Part\AdvLayNav\Observer\ProcessLayoutRenderElementObserver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $observer;

    public function setUp()
    {
        $this->eventObserverMock = $this->getMock(
            '\Magento\Framework\Event\Observer',
            ['getElementName', 'getEvent', 'getTransport'],
            [],
            '',
            false
        );
        $this->transportMock = $this->getMock('\Magento\Framework\DataObject', ['getData', 'setData'], [], '', false);
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->observer = $objectManager->getObject(
            'Part\AdvLayNav\Observer\ProcessLayoutRenderElementObserver',
            []
        );
    }

    /**
     * @dataProvider dataSurroundBlock
     */
    public function testSurroundBlock($elementName, $blockNameMatchCount, $output)
    {
        $this->eventObserverMock
            ->expects($this->once())
            ->method('getElementName')
            ->willReturn($elementName);
        $this->eventObserverMock
            ->expects($this->exactly($blockNameMatchCount))
            ->method('getTransport')
            ->willReturn($this->transportMock);
        $this->transportMock
            ->expects($this->exactly($blockNameMatchCount))
            ->method('getData')
            ->with('output')
            ->willReturn('someHtml');
        $this->transportMock
            ->expects($this->exactly($blockNameMatchCount))
            ->method('setData')
            ->with('output', $output);

        $this->observer->execute($this->eventObserverMock);
    }

    /**
     * @return array
     */
    public function dataSurroundBlock()
    {
        return [
            [
                'category.products.list',
                1,
                '<span id="advlaynav_category.products.list_before"></span>someHtml<span id="advlaynav_category.products.list_after"></span>',
            ],
            [
                'catalog.leftnav',
                1,
                '<span id="advlaynav_catalog.leftnav_before"></span>someHtml<span id="advlaynav_catalog.leftnav_after"></span>',
            ],
            [
                'some.blockname',
                0,
                '',
            ],
        ];
    }
}
