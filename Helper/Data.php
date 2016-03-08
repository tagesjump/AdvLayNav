<?php
/**
 * Copyright © PART <info@part-online.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 namespace Part\AdvLayNav\Helper;

 use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
 use Part\AdvLayNav\Model\AdvLayNav;

/**
 * Class Data
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var string
     */
    private $inputKey = AdvLayNav::INPUT_TYPE_KEY;

    /**
     * @param Attribute $attribute
     * @return bool
     */
    public function isAdvLayNavRangeSliderAttribute(Attribute $attribute)
    {
        $this->extractAdditionalDataEavAttribute($attribute);

        return $attribute->getData($this->inputKey) === AdvLayNav::INPUT_TYPE_RANGE_SLIDER;
    }

    /**
     * @param Attribute $attribute
     * @return $this
     */
    public function assembleAdditionalDataEavAttribute(Attribute $attribute)
    {
        $initialAdditionalData = [];
        $additionalData = (string) $attribute->getData('additional_data');
        if (!empty($additionalData)) {
            $additionalData = unserialize($additionalData);
            if (is_array($additionalData)) {
                $initialAdditionalData = $additionalData;
            }
        }

        $dataValue = $attribute->getData($this->inputKey);
        if (!is_null($dataValue)) {
            $initialAdditionalData[$this->inputKey] = $dataValue;
            $attribute->setData('additional_data', serialize($initialAdditionalData));
        }

        return $this;
    }

    /**
     * @param Attribute $attribute
     * @return void
     */
    private function extractAdditionalDataEavAttribute(Attribute $attribute)
    {
        if (!$attribute->hasData($this->inputKey)) {
            $additionalData = unserialize($attribute->getData('additional_data'));
            if (isset($additionalData[$this->inputKey])) {
                $attribute->setData($this->inputKey, $additionalData[$this->inputKey]);
            }
        }
    }
}
