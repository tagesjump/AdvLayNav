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
 */
class RenderLayered extends Template
{
    // @codingStandardsIgnoreStart
    /**
     * @var string
     */
    protected $_template = 'Part_AdvLayNav::product/layered/renderer.phtml';
    // @codingStandardsIgnoreEnd

    /**
     * @var \Magento\Eav\Model\Attribute
     */
    private $eavAttribute;

    /**
     * @var \Magento\Catalog\Model\Layer\Filter\AbstractFilter
     */
    private $filter;

    /**
     * @var float
     */
    private $maxValue;

    /**
     * @var float
     */
    private $minValue;

    /**
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
     * @return float
     */
    public function getMinValue()
    {
        $this->initMinMaxValues();

        return $this->minValue;
    }

    /**
     * @return float
     */
    public function getMaxValue()
    {
        $this->initMinMaxValues();

        return $this->maxValue;
    }

    /**
     * @return string
     */
    public function buildUrl()
    {
        $query = [$this->eavAttribute->getAttributeCode() => 'option_id_placeholder'];
        return $this->_urlBuilder->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
    }

    /**
     * @return void
     */
    private function initMinMaxValues()
    {
        if (is_null($this->minValue) || is_null($this->maxValue)) {
            $productCollection = $this->filter->getLayer()->getProductCollection();
            foreach ($productCollection as $product) {
                $productAttributeValue = $product->getData($this->eavAttribute->getAttributeCode());
                if (is_null($this->minValue) || $this->minValue > $productAttributeValue) {
                    $this->minValue = $productAttributeValue;
                }
                if (is_null($this->maxValue) || $this->maxValue < $productAttributeValue) {
                    $this->maxValue = $productAttributeValue;
                }
            }
            $this->maxValue++;
        }
    }
}
