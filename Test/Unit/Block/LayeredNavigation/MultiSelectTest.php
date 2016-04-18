<?php
/**
 * Copyright © PART <info@part-online.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Part\AdvLayNav\Test\Unit\Block\LayeredNavigation;

/**
 * Class MultiSelectTest
 */
class MultiSelectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $contextMock;

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
        $this->filterMock = $this->getMock(
            'Magento\Catalog\Model\Layer\Filter\AbstractFilter',
            ['getItems'],
            [],
            '',
            false
        );
        $this->block = $this->getMock(
            '\Part\AdvLayNav\Block\LayeredNavigation\MultiSelect',
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

    public function testGetFilterItems()
    {
        $filterItems = ['filterItems'];
        $this->filterMock->expects($this->once())->method('getItems')->willReturn($filterItems);

        $this->assertEquals($filterItems, $this->block->getFilterItems());
    }
}
