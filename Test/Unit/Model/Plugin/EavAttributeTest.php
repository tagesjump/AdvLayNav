<?php
/**
 * Copyright © PART <info@part-online.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Part\AdvLayNav\Test\Unit\Model\Plugin;

/**
 * Class EavAttributeTest
 */
class EavAttributeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $helperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $attributeMock;

    /**
     * @var \Part\AdvLayNav\Model\Plugin\EavAttribute
     */
    private $eavAttributePlugin;

    public function setUp()
    {
        $this->helperMock = $this->getMock('Part\AdvLayNav\Helper\Data', [], [], '', false);
        $this->attributeMock = $this->getMock(
            '\Magento\Catalog\Model\ResourceModel\Eav\Attribute',
            null,
            [],
            '',
            false
        );
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->eavAttributePlugin = $objectManager->getObject(
            'Part\AdvLayNav\Model\Plugin\EavAttribute',
            [
                'advLayNavHelper' => $this->helperMock,
            ]
        );
    }

    public function testBeforeSave()
    {
        $this->helperMock->expects($this->once())
            ->method('assembleAdditionalDataEavAttribute')
            ->with($this->attributeMock);
        $this->eavAttributePlugin->beforeSave($this->attributeMock);
    }
}
