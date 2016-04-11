<?php
/**
 * Copyright © PART <info@part-online.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Part\AdvLayNav\Test\Unit\Model\Plugin;

/**
 * Class PageTest
 */
class PageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var \Magento\Framework\Event\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $eventManagerMock;

    /**
     * @var \Magento\Framework\View\Element\Template\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    private $contextMock;

    /**
     * @var \Magento\Framework\View\Result\Page|\PHPUnit_Framework_MockObject_MockObject
     */
    private $pageMock;

    /**
     * @var \Closure
     */
    private $closureMock;

    /**
     * @var \Magento\Framework\App\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $responseMock;

    /**
     * @var \Part\AdvLayNav\Model\Plugin\Page
     */
    private $pagePlugin;

    public function setUp()
    {
        $this->requestMock = $this->getMock(
            '\Magento\Framework\App\Request\Http',
            ['getParam'],
            [],
            '',
            false
        );
        $this->eventManagerMock = $this->getMock(
            '\Magento\Framework\Event\Manager',
            ['dispatch'],
            [],
            '',
            false
        );
        $this->contextMock = $this->getMock(
            '\Magento\Framework\View\Element\Template\Context',
            ['getRequest', 'getEventManager'],
            [],
            '',
            false
        );
        $this->contextMock->expects($this->any())->method('getRequest')->willReturn($this->requestMock);
        $this->contextMock->expects($this->any())->method('getEventManager')->willReturn($this->eventManagerMock);
        $this->pageMock = $this->getMock(
            '\Magento\Framework\View\Result\Page',
            ['getLayout'],
            [],
            '',
            false
        );
        $this->closureMock = function () {
            return $this->responseMock;
        };
        $this->responseMock = $this->getMock(
            '\Magento\Framework\App\Response\Http',
            ['appendBody'],
            [],
            '',
            false
        );
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->pagePlugin = $objectManager->getObject(
            '\Part\AdvLayNav\Model\Plugin\Page',
            [
                'context' => $this->contextMock,
            ]
        );
    }

    public function testAroundRenderResultTrue()
    {
        $this->requestMock
            ->expects($this->exactly(3))
            ->method('getParam')
            ->will($this->returnValueMap([
                ['advLayNavAjax', null, '1'],
                ['productListBlockName', null, 'category.products.list'],
                ['navigationBlockName', null, 'catalog.leftnav'],
            ]));
        $layoutMock = $this->getMock(
            '\Magento\Framework\View\Layout',
            ['getBlock'],
            [],
            '',
            false
        );
        $productListBlockMock = $this->getMock(
            '\Magento\Catalog\Block\Product\ListProduct',
            ['toHtml'],
            [],
            '',
            false
        );
        $productListBlockMock
            ->expects($this->once())
            ->method('toHtml')
            ->willReturn(' some&advLayNavAjax=1Html productList&amp;advLayNavAjax=1 ');
        $leftnavBlockMock = $this->getMock(
            '\Magento\LayeredNavigation\Block\Navigation',
            ['toHtml'],
            [],
            '',
            false
        );
        $leftnavBlockMock
            ->expects($this->once())
            ->method('toHtml')
            ->willReturn(' some&advLayNavAjax=1Html leftNav&amp;advLayNavAjax=1 ');
        $layoutMock
            ->expects($this->exactly(2))
            ->method('getBlock')
            ->will($this->returnValueMap([
                ['category.products.list', $productListBlockMock],
                ['catalog.leftnav', $leftnavBlockMock],
            ]));
        $this->pageMock->expects($this->once())->method('getLayout')->willReturn($layoutMock);
        $this->pageMock
            ->expects($this->once())
            ->method('getLayout')
            ->willReturn($layoutMock);
        $this->responseMock
            ->expects($this->once())
            ->method('appendBody')
            ->with('["someHtml productList","someHtml leftNav"]');

        $this->pagePlugin->aroundRenderResult($this->pageMock, $this->closureMock, $this->responseMock);
    }

    public function testAroundRenderResultFalse()
    {
        $this->requestMock
            ->expects($this->once())
            ->method('getParam')
            ->with('advLayNavAjax')
            ->willReturn(null);

        $result = $this->pagePlugin->aroundRenderResult($this->pageMock, $this->closureMock, $this->responseMock);
        $this->assertEquals($result, $this->responseMock);
    }
}
