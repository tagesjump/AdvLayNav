<?php
/**
 * Copyright © PART <info@part-online.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Part\AdvLayNav\Test\Unit\Model\Plugin;

/**
 * Class FilterRendererTest
 */
class FilterRendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Closure
     */
    private $closureMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $layoutMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $blockMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $helperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $filterMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $filterRendererMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $attributeMock;

    /**
     * @var \Part\AdvLayNav\Model\Plugin\FilterRenderer
     */
    private $filterRendererPlugin;

    public function setUp()
    {
        $this->layoutMock = $this->getMock(
            '\Magento\Framework\View\Layout',
            ['createBlock'],
            [],
            '',
            false
        );
        $this->blockMock = $this->getMock(
            '\Part\AdvLayNav\Block\LayeredNavigation\RenderLayered',
            ['setAdvLayNavFilter', 'toHtml'],
            [],
            '',
            false
        );
        $this->helperMock = $this->getMock(
            '\Part\AdvLayNav\Helper\Data',
            ['isAdvLayNavRangeSliderAttribute', 'isAdvLayNavMultiSelectAttribute'],
            [],
            '',
            false
        );
        $this->filterMock = $this->getMock(
            '\Magento\Catalog\Model\Layer\Filter\AbstractFilter',
            ['getAttributeModel', 'hasAttributeModel'],
            [],
            '',
            false
        );
        $this->filterRendererMock = $this->getMock(
            '\Magento\LayeredNavigation\Block\Navigation\FilterRenderer',
            [],
            [],
            '',
            false
        );
        $this->closureMock = function () {
            return $this->filterMock;
        };
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->filterRendererPlugin = $objectManager->getObject(
            'Part\AdvLayNav\Model\Plugin\FilterRenderer',
            [
                'layout' => $this->layoutMock,
                'advLayNavHelper' => $this->helperMock,
            ]
        );
        $this->attributeMock = $this->getMock(
            '\Magento\Catalog\Model\ResourceModel\Eav\Attribute',
            null,
            [],
            '',
            false
        );
        $this->filterMock->expects($this->atLeastOnce())->method('getAttributeModel')->willReturn($this->attributeMock);
        $this->filterMock->expects($this->once())->method('hasAttributeModel')->willReturn(true);
    }

    public function testAroundRenderRangeSlider()
    {
        $this->helperMock
            ->expects($this->once())
            ->method('isAdvLayNavRangeSliderAttribute')
            ->with($this->attributeMock)
            ->willReturn(true);
        $this->layoutMock->expects($this->once())
            ->method('createBlock')
            ->with('Part\AdvLayNav\Block\LayeredNavigation\RangeSlider')
            ->willReturn($this->blockMock);
        $this->blockMock->expects($this->once())->method('setAdvLayNavFilter')->will($this->returnSelf());

        $this->filterRendererPlugin->aroundRender($this->filterRendererMock, $this->closureMock, $this->filterMock);
    }

    public function testAroundRenderMultiSelect()
    {
        $this->helperMock
            ->expects($this->once())
            ->method('isAdvLayNavRangeSliderAttribute')
            ->with($this->attributeMock)
            ->willReturn(false);
        $this->helperMock
            ->expects($this->once())
            ->method('isAdvLayNavMultiSelectAttribute')
            ->with($this->attributeMock)
            ->willReturn(true);
        $this->layoutMock->expects($this->once())
            ->method('createBlock')
            ->with('Part\AdvLayNav\Block\LayeredNavigation\MultiSelect')
            ->willReturn($this->blockMock);
        $this->blockMock->expects($this->once())->method('setAdvLayNavFilter')->will($this->returnSelf());

        $this->filterRendererPlugin->aroundRender($this->filterRendererMock, $this->closureMock, $this->filterMock);
    }

    public function testAroundRenderFalse()
    {
        $this->helperMock
            ->expects($this->once())
            ->method('isAdvLayNavRangeSliderAttribute')
            ->with($this->attributeMock)
            ->willReturn(false);
        $this->helperMock
            ->expects($this->once())
            ->method('isAdvLayNavMultiSelectAttribute')
            ->with($this->attributeMock)
            ->willReturn(false);

        $result = $this->filterRendererPlugin->aroundRender(
            $this->filterRendererMock,
            $this->closureMock,
            $this->filterMock
        );
        $this->assertEquals($result, $this->filterMock);
    }
}
