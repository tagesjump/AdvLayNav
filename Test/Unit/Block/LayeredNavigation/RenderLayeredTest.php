<?php
/**
 * Copyright © PART <info@part-online.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Part\AdvLayNav\Test\Unit\Block\LayeredNavigation;

/**
 * Class RenderLayeredTest
 */
class RenderLayeredTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $contextMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $eavAttributeMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $filterMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $block;

    public function setUp()
    {
        $this->contextMock = $this->getMock('\Magento\Framework\View\Element\Template\Context', [], [], '', false);
        $this->eavAttributeMock = $this->getMock('\Magento\Eav\Model\Entity\Attribute', null, [], '', false);
        $this->filterMock = $this->getMock('Magento\Catalog\Model\Layer\Filter\AbstractFilter', [], [], '', false);
        $this->block = $this->getMock(
            '\Part\AdvLayNav\Block\LayeredNavigation\RenderLayered',
            ['filter', 'eavAttribute'],
            [
                $this->contextMock,
                $this->eavAttributeMock,
                [],
            ],
            '',
            true
        );
    }

    public function testSetAdvLayNavFilter()
    {
        $this->block->method('filter')->willReturn($this->filterMock);
        $eavAttribute = $this->getMock('\Magento\Catalog\Model\ResourceModel\Eav\Attribute', null, [], '', false);
        $this->filterMock->expects($this->once())->method('getAttributeModel')->willReturn($eavAttribute);
        $this->block->method('eavAttribute')->willReturn($eavAttribute);
        $result = $this->block->setAdvLayNavFilter($this->filterMock);
        $this->assertEquals($result, $this->block);
    }
}
