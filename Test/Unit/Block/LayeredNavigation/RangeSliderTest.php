<?php
/**
 * Copyright © PART <info@part-online.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Part\AdvLayNav\Test\Unit\Block\LayeredNavigation;

/**
 * Class RangeSliderTest
 */
class RangeSliderTest extends \PHPUnit_Framework_TestCase
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
    private $sessionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $storeMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $prodCollMock;

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
        $this->urlBuilderMock = $this->getMock('\Magento\Framework\Url', ['getUrl'], [], '', false);
        $this->contextMock->expects($this->any())->method('getUrlBuilder')->willReturn($this->urlBuilderMock);
        $this->sessionMock = $this->getMock(
            '\Magento\Framework\Session\SessionManager',
            ['getCustomerGroupId'],
            [],
            '',
            false
        );
        $this->contextMock->expects($this->any())->method('getSession')->willReturn($this->sessionMock);
        $storeManagerMock = $this->getMock('\Magento\Store\Model\StoreManager', ['getStore'], [], '', false);
        $this->storeMock = $this->getMock('\Magento\Store\Model\Store', ['getWebsiteId'], [], '', false);
        $storeManagerMock->expects($this->any())->method('getStore')->willReturn($this->storeMock);
        $this->contextMock->expects($this->any())->method('getStoreManager')->willReturn($storeManagerMock);
        $this->requestMock = $this->getMock('\Magento\Framework\App\Request\Http', ['getParam'], [], '', false);
        $this->contextMock->expects($this->any())->method('getRequest')->willReturn($this->requestMock);
        $this->prodCollMock = $this->getMock(
            '\Magento\Catalog\Model\ResourceModel\Product\Collection',
            ['addPriceData', 'getMinPrice', 'getMaxPrice'],
            [],
            '',
            false
        );
        $categoryMock = $this->getMock('\Magento\Catalog\Model\Category', ['getProductCollection'], [], '', false);
        $categoryMock->expects($this->any())->method('getProductCollection')->willReturn($this->prodCollMock);
        $layerMock = $this->getMock('\Magento\Catalog\Model\Layer', ['getCurrentCategory'], [], '', false);
        $layerMock->expects($this->any())->method('getCurrentCategory')->willReturn($categoryMock);
        $this->filterMock = $this->getMock(
            'Magento\Catalog\Model\Layer\Filter\AbstractFilter',
            ['getLayer', 'getResetValue', 'getRequestVar'],
            [],
            '',
            false
        );
        $this->filterMock->expects($this->any())->method('getLayer')->willReturn($layerMock);
        $this->block = $this->getMock(
            '\Part\AdvLayNav\Block\LayeredNavigation\RangeSlider',
            ['filter'],
            [
                $this->contextMock,
                [],
            ],
            '',
            true
        );
        $this->block->setAdvLayNavFilter($this->filterMock);
    }

    public function testGetMinMaxValue()
    {
        $this->sessionMock->expects($this->once())->method('getCustomerGroupId')->willReturn(5);
        $this->storeMock->expects($this->once())->method('getWebsiteId')->willReturn(3);
        $this->prodCollMock->expects($this->once())->method('addPriceData')->with(5, 3);
        $this->prodCollMock->expects($this->once())->method('getMinPrice')->willReturn(6);
        $this->prodCollMock->expects($this->once())->method('getMaxPrice')->willReturn(15);
        $this->assertSame(6, $this->block->getMinValue());
        $this->assertSame(15, $this->block->getMaxValue());
    }

    /**
     * @dataProvider dataGetLeftRightValue
     */
    public function testGetLeftRightValue($param, $minValue, $maxValue, $expectedLeft, $expectedRight)
    {
        $this->prodCollMock->expects($this->any())->method('getMinPrice')->willReturn($minValue);
        $this->prodCollMock->expects($this->any())->method('getMaxPrice')->willReturn($maxValue);
        $this->filterMock->expects($this->any())->method('getRequestVar')->willReturn('price');
        $this->requestMock->expects($this->once())->method('getParam')->with('price')->willReturn($param);
        $this->assertEquals($expectedLeft, $this->block->getLeftValue());
        $this->assertEquals($expectedRight, $this->block->getRightValue());
    }

    public function testGetOptionsPlaceholderUrl()
    {
        $args = [
            '_current' => true,
            '_use_rewrite' => true,
            '_query' => [
                'price' => 'option_id_placeholder',
            ],
        ];
        $this->filterMock->expects($this->once())->method('getRequestVar')->willReturn('price');
        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with('*/*/*', $args)
            ->willReturn('http://example.com/');
        $this->assertSame('http://example.com/', $this->block->getOptionsPlaceholderUrl());
    }

    public function testGetRemoveUrl()
    {
        $args = [
            '_current' => true,
            '_use_rewrite' => true,
            '_query' => [
                'price' => '50-102',
            ],
        ];
        $this->filterMock->expects($this->once())->method('getRequestVar')->willReturn('price');
        $this->filterMock->expects($this->once())->method('getResetValue')->willReturn('50-102');
        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with('*/*/*', $args)
            ->willReturn('http://example.com/');
        $this->assertSame('http://example.com/', $this->block->getRemoveUrl());
    }

    /**
     * @return array
     */
    public function dataGetLeftRightValue()
    {
        return [
            ['', 3, 9, 3, 9],
            ['45-', 2, 999, 45, 999],
            ['3-67', 0, 101, 3, 67],
        ];
    }

    public function testGetFilterRequestVar()
    {
        $this->filterMock->expects($this->once())->method('getRequestVar')->willReturn('price');

        $this->assertSame('price', $this->block->getFilterRequestVar());
    }
}
