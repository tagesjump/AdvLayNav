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
        $items = $this->filter->getItems();
        return $items['min'];
    }

    /**
     * Returns the maximum value of the attribute for the current product collection.
     *
     * @return float
     */
    public function getMaxValue()
    {
        $items = $this->filter->getItems();
        return $items['max'];
    }

    /**
     * Returns the left value of the slider.
     *
     * @return float
     */
    public function getLeftValue()
    {
        $items = $this->filter->getItems();
        return $items['from'];
    }

    /**
     * Returns the right value of the slider.
     *
     * @return flaot
     */
    public function getRightValue()
    {
        $items = $this->filter->getItems();
        return $items['to'];
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
}
