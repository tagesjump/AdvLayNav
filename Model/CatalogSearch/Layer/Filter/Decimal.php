<?php
/**
 * Copyright © PART <info@part-online.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Part\AdvLayNav\Model\CatalogSearch\Layer\Filter;

/**
 * Class Decimal
 */
class Decimal extends \Magento\CatalogSearch\Model\Layer\Filter\Decimal
{
    /**
     * The AdvLayNav helper.
     *
     * @var \Part\AdvLayNav\Helper\Data
     */
    private $advLayNavHelper;

    private $fromValue;

    private $toValue;

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Layer $layer
     * @param \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder
     * @param \Magento\Catalog\Model\ResourceModel\Layer\Filter\DecimalFactory $filterDecimalFactory
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Catalog\Model\ResourceModel\Layer\Filter\DecimalFactory $filterDecimalFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Part\AdvLayNav\Helper\Data $advLayNavHelper,
        array $data = []
    ) {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $filterDecimalFactory,
            $priceCurrency,
            $data
        );
        $this->advLayNavHelper = $advLayNavHelper;
    }

    /**
     * Apply decimal range filter
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        $attribute = $this->getAttributeModel();
        if ($this->advLayNavHelper->isAdvLayNavRangeSliderAttribute($attribute)) {
            $filter = $request->getParam($this->getRequestVar());
            if (!$filter || is_array($filter)) {
                return $this;
            }

            list($from, $to) = explode('-', $filter);
            $this->fromValue = $from;
            $this->toValue = $to;

            $this->getLayer()
                ->getProductCollection()
                ->addFieldToFilter(
                    $this->getAttributeModel()->getAttributeCode(),
                    ['from' => $from, 'to' => $to]
                );

            $this->getLayer()->getState()->addFilter(
                $this->_createItem($this->renderRangeLabel(empty($from) ? 0 : $from, $to), $filter)
            );

            return $this;
        }
        return parent::apply($request);
    }

    /**
     * Get data array for building attribute filter items
     *
     * @return array
     */
    protected function _initItems()
    {
        $attribute = $this->getAttributeModel();
        if ($this->advLayNavHelper->isAdvLayNavRangeSliderAttribute($attribute)) {
            if (!$this->_items) {
                $attributeCode = $attribute->getAttributeCode();
                $layer = $this->getLayer();
                /** @var \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $productCollection */
                $productCollection = clone($layer->getProductCollection());
                if ($this->advLayNavHelper->isFilterApplied($layer->getState(), $attributeCode)) {
                    $productCollection = $layer->getCurrentCategory()->getProductCollection();
                }
                $productCollection->addFieldToSelect($attributeCode);
                $minValue = INF;
                $maxValue = -INF;
                foreach ($productCollection as $product) {
                    $attributeValue = $product->getData($attributeCode);
                    if (strlen((String) $attributeValue)) {
                        if ($minValue > $attributeValue) {
                            $minValue = $attributeValue;
                        }
                        if ($maxValue < $attributeValue) {
                            $maxValue = $attributeValue;
                        }
                    }
                }
                if (!$this->fromValue) {
                    $this->fromValue = $minValue;
                }
                if (!$this->toValue) {
                    $this->toValue = $maxValue;
                }

                if ($minValue < INF && $maxValue > -INF && $minValue != $maxValue) {
                    $this->_items = [
                        'min' => $minValue,
                        'from' => $this->fromValue,
                        'to' => $this->toValue,
                        'max' => $maxValue,
                    ];
                } else {
                    $this->_items = [];
                }
            }
            return $this;
        }
        return parent::_initItems();
    }
}
