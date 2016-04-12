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
    private $urlBuilderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $eavAttributeMock;

    /**
     * @var string
     */
    private $attributeCode = 'super_mega_attribute';

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $prodCollMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $layerMock;

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
        $this->urlBuilderMock = $this->getMock(
            '\Magento\Framework\Url',
            ['getCurrentUrl', 'getRedirectUrl', 'getUrl'],
            [],
            '',
            false
        );
        $this->contextMock->expects($this->any())->method('getUrlBuilder')->willReturn($this->urlBuilderMock);
        $this->eavAttributeMock = $this->getMock(
            '\Magento\Eav\Model\Entity\Attribute',
            ['getAttributeCode'],
            [],
            '',
            false
        );
        $this->eavAttributeMock->expects($this->any())->method('getAttributeCode')->willReturn($this->attributeCode);
        $productMock1 = $this->getMock('\Magento\Catalog\Model\Product', ['getData'], [], '', false);
        $productMock1->expects($this->any())->method('getData')->with($this->attributeCode)->willReturn(45);
        $productMock2 = $this->getMock('\Magento\Catalog\Model\Product', ['getData'], [], '', false);
        $productMock2->expects($this->any())->method('getData')->with($this->attributeCode)->willReturn(12);
        $productMock3 = $this->getMock('\Magento\Catalog\Model\Product', ['getData'], [], '', false);
        $productMock3->expects($this->any())->method('getData')->with($this->attributeCode)->willReturn(-5);
        $this->prodCollMock = $this->getMock(
            '\Magento\Catalog\Model\ResourceModel\Product\Collection',
            ['getIterator'],
            [],
            '',
            false
        );
        $this->prodCollMock->expects($this->any())->method('getIterator')->willReturn(
            new \ArrayIterator(
                [
                    $productMock1,
                    $productMock2,
                    $productMock3,
                ]
            )
        );
        $this->layerMock = $this->getMock('\Magento\Catalog\Model\Layer', ['getProductCollection'], [], '', false);
        $this->layerMock->expects($this->any())->method('getProductCollection')->willReturn($this->prodCollMock);
        $this->filterMock = $this->getMock(
            'Magento\Catalog\Model\Layer\Filter\AbstractFilter',
            ['getLayer', 'getAttributeModel', 'getRemoveUrl'],
            [],
            '',
            false
        );
        $this->filterMock->expects($this->any())->method('getLayer')->willReturn($this->layerMock);
        $this->filterMock->expects($this->any())->method('getAttributeModel')->willReturn($this->eavAttributeMock);
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

    public function testGetMinValue()
    {
        $this->block->setAdvLayNavFilter($this->filterMock);
        $this->assertSame(-5, $this->block->getMinValue());
    }

    public function testGetMaxValue()
    {
        $this->block->setAdvLayNavFilter($this->filterMock);
        $this->assertSame(46, $this->block->getMaxValue());
    }

    public function testGetOptionsPlaceholderUrl()
    {
        $args = [
            '_current' => true,
            '_use_rewrite' => true,
            '_query' => [
                $this->attributeCode => 'option_id_placeholder',
            ],
        ];
        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with('*/*/*', $args)
            ->willReturn('http://example.com/');
        $this->assertSame('http://example.com/', $this->block->getOptionsPlaceholderUrl());
    }

    public function testGetRemoveUrl()
    {
        $this->filterMock->expects($this->once())
            ->method('getRemoveUrl')
            ->willReturn('https://remove-that-shit.dev/');
        $this->block->setAdvLayNavFilter($this->filterMock);
        $this->assertSame('https://remove-that-shit.dev/', $this->block->getRemoveUrl());
    }
}
