<?php
/**
 * Copyright © PART <info@part-online.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Part\AdvLayNav\Test\Unit\Observer;

use Part\AdvLayNav\Model\AdvLayNav;

/**
 * Class AddFieldsToAttributeObserverTest
 */
class AddFieldsToAttributeObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Module\Manager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $moduleManagerMock;

    /**
     * @var \Magento\Framework\Data\Form|\PHPUnit_Framework_MockObject_MockObject
     */
    private $formMock;

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
            ['getForm', 'getEvent', 'getAttribute'],
            [],
            '',
            false
        );
        $this->formMock = $this->getMock('\Magento\Framework\Data\Form', ['getElement'], [], '', false);
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->observer = $objectManager->getObject(
            'Part\AdvLayNav\Observer\AddFieldsToAttributeObserver',
            [
                'moduleManager' => $this->moduleManagerMock,
            ]
        );
    }

    /**
     * @dataProvider dataAddFields
     */
    public function testAddFields($isOutputEnabled, $getFormCount, $getElementCount, $addFieldCount)
    {
        $this->moduleManagerMock
            ->expects($this->once())
            ->method('isOutputEnabled')
            ->willReturn($isOutputEnabled);
        $this->eventObserverMock
            ->expects($this->exactly($getFormCount))
            ->method('getForm')
            ->willReturn($this->formMock);
        $element = $this->getMock('Magento\Framework\Data\Form\Element\AbstractElement', [], [], '', false);
        $this->formMock
            ->expects($this->exactly($getElementCount))
            ->method('getElement')
            ->with('front_fieldset')
            ->willReturn($element);
        $element->expects($this->exactly($addFieldCount))
            ->method('addField')
            ->with(
                AdvLayNav::INPUT_TYPE_KEY,
                'select',
                [
                    'name' => AdvLayNav::INPUT_TYPE_KEY,
                    'label' => __('Show as AdvLayNav Input'),
                    'title' => __('Show as AdvLayNav Input'),
                    'values' => [
                        AdvLayNav::INPUT_TYPE_NONE => __('No'),
                        AdvLayNav::INPUT_TYPE_RANGE_SLIDER => __('Range Slider'),
                        AdvLayNav::INPUT_TYPE_MULTI_SELECT => __('Multiselect'),
                    ],
                ]
            );

        $this->observer->execute($this->eventObserverMock);
    }

    /**
     * @return array
     */
    public function dataAddFields()
    {
        return [
            [
                false,
                0,
                0,
                0
            ],
            [
                true,
                1,
                1,
                1,
            ],
        ];
    }
}
