<?php
/**
 * Copyright © PART <info@part-online.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Part\AdvLayNav\Block\LayeredNavigation;

use Magento\Eav\Model\Entity\Attribute;
use Magento\Framework\View\Element\Template;

/**
 * Class RenderLayered
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class RenderLayered extends Template
{
    // @codingStandardsIgnoreStart
    /**
     * The template file for our RenderLayered.
     *
     * @var string
     */
    protected $_template = 'Part_AdvLayNav::product/layered/renderer.phtml';
    // @codingStandardsIgnoreEnd

    /**
     * The attribute from the filter of the RenderLayered.
     *
     * @var \Magento\Eav\Model\Attribute
     */
    private $eavAttribute;

    /**
     * The filter of the RenderLayered.
     *
     * @var \Magento\Catalog\Model\Layer\Filter\AbstractFilter
     */
    private $filter;

    private $storeManager;

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
     * Creates an RenderLayered object.
     *
     * @param Template\Context $context
     * @param Attribute $eavAttribute
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Attribute $eavAttribute,
        array $data = []
    ) {
        $this->eavAttribute = $eavAttribute;
        parent::__construct($context, $data);
    }

    /**
     * Sets the filter on this RenderLayered object.
     *
     * @param \Magento\Catalog\Model\Layer\Filter\AbstractFilter $filter
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setAdvLayNavFilter(\Magento\Catalog\Model\Layer\Filter\AbstractFilter $filter)
    {
        $this->filter = $filter;
        $this->eavAttribute = $filter->getAttributeModel();

        return $this;
    }

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

    public function getLeftValue()
    {
        $filter = $this->_request->getParam($this->filter->getRequestVar());
        $filter = explode('-', $filter);
        if (count($filter) != 2) {
            return $this->getMinValue();
        }
        return $filter[0];
    }

    public function getRightValue()
    {
        $filter = $this->_request->getParam($this->filter->getRequestVar());
        $filter = explode('-', $filter);
        if (count($filter) != 2) {
            return $this->getMaxValue();
        }
        return $filter[1];
    }

    /**
     * Builds a url for the current attribute with option_id_placeholder as placeholder.
     *
     * @return string
     */
    public function getOptionsPlaceholderUrl()
    {
        $query = [$this->eavAttribute->getAttributeCode() => 'option_id_placeholder'];
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
            $productCollection->addPriceData(
                $this->_session->getCustomerGroupId(),
                $this->_storeManager->getStore()->getWebsiteId()
            );
            $this->minValue = $productCollection->getMinPrice();
            $this->maxValue = $productCollection->getMaxPrice();
        }
    }
}
