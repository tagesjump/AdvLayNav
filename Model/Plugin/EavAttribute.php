<?php
/**
 * Copyright © PART <info@part-online.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Part\AdvLayNav\Model\Plugin;

/**
 * Class EavAttribute
 */
class EavAttribute
{
    /**
     * The AdvLayNav helper.
     *
     * @var \Part\AdvLayNav\Helper\Data
     */
    private $advLayNavHelper;

    /**
     * Creates an EavAttribute plugin that surrounds the beforeSave function of an EavAttribute.
     *
     * @param \Part\AdvLayNav\Helper\Data $advLayNavHelper
     */
    public function __construct(\Part\AdvLayNav\Helper\Data $advLayNavHelper)
    {
        $this->advLayNavHelper = $advLayNavHelper;
    }

    /**
     * Set base data to Attribute
     *
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute
     * @return void
     */
    public function beforeSave(\Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute)
    {
        $this->advLayNavHelper->assembleAdditionalDataEavAttribute($attribute);
    }
}
