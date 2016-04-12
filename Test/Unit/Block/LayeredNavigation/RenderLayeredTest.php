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
    private $sessionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $storeMock;

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
        $this->urlBuilderMock = $this->getMock(
            '\Magento\Framework\Url',
            ['getCurrentUrl', 'getRedirectUrl', 'getUrl'],
            [],
            '',
            false
        );
        $this->contextMock->expects($this->any())->method('getUrlBuilder')->willReturn($this->urlBuilderMock);
        $this->sessionMock = $this->getMock(
            '\Magento\Framework\Session\SessionManager',
            ['getCustomerGroupId'],
            [],
            '',
            false
        );
        $this->contextMock->expects($this->any())->method('getSession')->willReturn($this->sessionMock);
        $storeManagerMock = $this->getMock(
            '\Magento\Store\Model\StoreManager',
            ['getStore'],
            [],
            '',
            false
        );
        $this->storeMock = $this->getMock(
            '\Magento\Store\Model\Store',
            ['getWebsiteId'],
            [],
            '',
            false
        );
        $storeManagerMock->expects($this->any())->method('getStore')->willReturn($this->storeMock);
        $this->contextMock->expects($this->any())->method('getStoreManager')->willReturn($storeManagerMock);
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
            '\Part\AdvLayNav\Block\LayeredNavigation\RenderLayered',
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

    public function testSetAdvLayNavFilter()
    {
        $result = $this->block->setAdvLayNavFilter($this->filterMock);
        $this->assertEquals($result, $this->block);
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
}
