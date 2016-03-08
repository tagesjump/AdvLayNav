<?php
/**
 * Copyright © PART <info@part-online.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Part\AdvLayNav\Test\Unit\Helper;

/**
 * Class DataTest
 */
class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Catalog\Model\ResourceModel\Eav\Attribute
     */
    private $attributeMock;

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager|\Part\AdvLayNav\Helper\Data
     */
    private $advLayNavHelper;

    public function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->attributeMock = $this->getMock(
            '\Magento\Catalog\Model\ResourceModel\Eav\Attribute',
            ['setData', 'getData', 'hasData'],
            [],
            '',
            false
        );

        $this->advLayNavHelper = $objectManager->getObject('\Part\AdvLayNav\Helper\Data', []);
    }

    /**
     * @dataProvider dataAssembleAdditionalDataEavAttribute
     */
    public function testAssembleAdditionalDataEavAttribute($dataFromDb, $attributeData)
    {
        $this->attributeMock
            ->expects($this->at(0))
            ->method('getData')
            ->with('additional_data')
            ->will($this->returnValue($dataFromDb));

        $i = 1;
        foreach ($attributeData as $key => $value) {
            $this->attributeMock
                ->expects($this->at($i))
                ->method('getData')
                ->with($key)
                ->willReturn($value);
            $i++;
        }

        if (count($attributeData)) {
            $this->attributeMock->expects($this->once())->method('setData');
        }

        $this->advLayNavHelper->assembleAdditionalDataEavAttribute($this->attributeMock);
    }

    /**
     * @return array
     */
    public function dataAssembleAdditionalDataEavAttribute()
    {
        return [
            [
                serialize(['advlaynav_input_type' => 'slider']),
                [
                    'advlaynav_input_type' => 'slider',
                ],
            ],
            [
                null,
                [],
            ],
        ];
    }

    /**
     * @dataProvider dataIsAdvLayNavRangeSliderAttributeFalse
     */
    public function testIsAdvLayNavRangeSliderAttributeFalse($data, $setDataCount, $type, $boolResult)
    {
        $this->attributeMock->method('hasData')->with('advlaynav_input_type')->willReturn(false);
        $this->attributeMock->expects($this->exactly(2))->method('getData')->withConsecutive(
            ['additional_data'],
            ['advlaynav_input_type']
        )->willReturnOnConsecutiveCalls($data, $type);
        $this->attributeMock
            ->expects($this->exactly($setDataCount))
            ->method('setData');

        $result = $this->advLayNavHelper->isAdvLayNavRangeSliderAttribute($this->attributeMock);
        if ($boolResult) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    /**
     * @return array
     */
    public function dataIsAdvLayNavRangeSliderAttributeFalse()
    {
        return [
            [
                serialize(['advlaynav_input_type' => 'slider']),
                1,
                'range_slider',
                true,
            ],
            [
                null,
                0,
                'some_other_type',
                false,
            ],
        ];
    }

    /**
     * @dataProvider dataIsAdvLayNavRangeSliderAttribute
     */
    public function testIsAdvLayNavRangeSliderAttribute($type, $boolResult)
    {
        $this->attributeMock->method('hasData')->with('advlaynav_input_type')->willReturn(true);
        $this->attributeMock
            ->expects($this->once())
            ->method('getData')
            ->with('advlaynav_input_type')
            ->willReturn($type);
        $result = $this->advLayNavHelper->isAdvLayNavRangeSliderAttribute($this->attributeMock);
        if ($boolResult) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    /**
     * @return array
     */
    public function dataIsAdvLayNavRangeSliderAttribute()
    {
        return [
            [
                'range_slider',
                true,
            ],
            [
                'some_other_type',
                false,
            ],
        ];
    }
}
