<?php
/**
 * Copyright © PART <info@part-online.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Part\AdvLayNav\Block\LayeredNavigation;

/**
 * Class RangeSlider
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class RangeSlider extends AbstractRenderLayered
{
    // @codingStandardsIgnoreStart
    /**
     * The template file for our RenderLayered.
     *
     * @var string
     */
    protected $_template = 'Part_AdvLayNav::product/layered/rangeslider.phtml';
    // @codingStandardsIgnoreEnd

    /**
     * The maximum value of the attribute.
     *
     * @var float
     */
    private $maxValue;

    /**
     * The minimum value of the attribute.
     *
     * @var float
     */
    private $minValue;

    /**
     * The left value of the attribute slider.
     *
     * @var flaot
     */
    private $leftValue;

    /**
     * The right value of the attribute slider.
     *
     * @var flaot
     */
    private $rightValue;

    /**
     * Returns the minimum value of the attribute for the current product collection.
     *
     * @return float
     */
    public function getMinValue()
    {
        $this->initMinMaxValues();

        return $this->minValue;
    }

    /**
     * Returns the maximum value of the attribute for the current product collection.
     *
     * @return float
     */
    public function getMaxValue()
    {
        $this->initMinMaxValues();

        return $this->maxValue;
    }

    /**
     * Returns the left value of the slider.
     *
     * @return float
     */
    public function getLeftValue()
    {
        $this->initLeftRightValue();

        return $this->leftValue;
    }

    /**
     * Returns the right value of the slider.
     *
     * @return flaot
     */
    public function getRightValue()
    {
        $this->initLeftRightValue();

        return $this->rightValue;
    }

    /**
     * Builds a url for the current attribute with option_id_placeholder as placeholder.
     *
     * @return string
     */
    public function getOptionsPlaceholderUrl()
    {
        $query = [$this->filter->getRequestVar() => 'option_id_placeholder'];
        return $this->_urlBuilder->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
    }

    /**
     * Builds url without the current attribute.
     *
     * @return string
     */
    public function getRemoveUrl()
    {
        $query = [$this->filter->getRequestVar() => $this->filter->getResetValue()];
        return $this->_urlBuilder->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
    }

    /**
     * Initializes the minimum and maximum value of the attribute and attaches them to the RenderLayered object.
     *
     * @return void
     */
    private function initMinMaxValues()
    {
        if (is_null($this->minValue) || is_null($this->maxValue)) {
            $productCollection = $this->filter->getLayer()->getCurrentCategory()->getProductCollection();
            $attributeCode = $this->filter->getAttributeModel()->getAttributeCode();
            if ($this->filter->getAttributeModel()->getAttributeCode() == 'price') {
                $productCollection->addPriceData(
                    $this->_session->getCustomerGroupId(),
                    $this->_storeManager->getStore()->getWebsiteId()
                );
                $this->minValue = $productCollection->getMinPrice();
                $this->maxValue = $productCollection->getMaxPrice();
            } else {
                $this->minValue = INF;
                $this->maxValue = -INF;
                $productCollection->addFieldToSelect($attributeCode);
                foreach ($productCollection as $product) {
                    $attributeValue = $product->getData($attributeCode);
                    if (strlen((String) $attributeValue)) {
                        if ($this->minValue > $attributeValue) {
                            $this->minValue = $attributeValue;
                        }
                        if ($this->maxValue < $attributeValue) {
                            $this->maxValue = $attributeValue;
                        }
                    }
                }
            }
        }
    }

    /**
     * Initializes the left & right value of the attribute filter.
     *
     * @return void
     */
    private function initLeftRightValue()
    {
        if (is_null($this->leftValue) || is_null($this->rightValue)) {
            $filter = $this->_request->getParam($this->filter->getRequestVar());
            $filter = explode('-', $filter);
            if (count($filter) != 2) {
                $this->leftValue = $this->getMinValue();
                $this->rightValue = $this->getMaxValue();
                return;
            }
            if (!$filter[1]) {
                $this->leftValue = $filter[0];
                $this->rightValue = $this->getMaxValue();
                return;
            }
            $this->leftValue = $filter[0];
            $this->rightValue = $filter[1];
        }
    }
}
