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

            $explode = explode('-', $filter);
            $this->fromValue = isset($explode[0]) ? $explode[0] : null;
            $this->toValue = isset($explode[1]) ? $explode[1] : null;

            $this->getLayer()
                ->getProductCollection()
                ->addFieldToFilter(
                    $this->getAttributeModel()->getAttributeCode(),
                    ['from' => $this->fromValue, 'to' => $this->toValue]
                );

            $this->getLayer()->getState()->addFilter(
                $this->_createItem(
                    $this->renderRangeLabel(empty($this->fromValue) ? 0 : $this->fromValue, $this->toValue),
                    $filter
                )
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
                $minMax = $this->getMinAndMaxValue($productCollection, $attribute);
                $minValue = $minMax[0];
                $maxValue = $minMax[1];
                if (!$this->fromValue) {
                    $this->fromValue = $minValue;
                }
                if (!$this->toValue) {
                    $this->toValue = $maxValue;
                }

                if ($minValue !== null && $maxValue !== null && $minValue != $maxValue) {
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

    private function getMinAndMaxValue(
        \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection,
        $attribute
    ) {
        $select = clone $productCollection->getSelect();
        $connection = $select->getConnection();
        $select->reset(\Magento\Framework\DB\Select::ORDER);
        $select->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $select->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
        $select->reset(\Magento\Framework\DB\Select::COLUMNS);
        if ($productCollection->isEnabledFlat()) {
            $attributeCode = $attribute->getAttributeCode();
            $select->columns(
                [
                    'min' => 'MIN(e.' . $attributeCode . ')',
                    'max' => 'MAX(e.' . $attributeCode . ')',
                ]
            );
            $select->where('e.' . $attributeCode . ' IS NOT NULL');
        } else {
            $select->join(
                ['decimal_index' => $productCollection->getTable('catalog_product_index_eav_decimal')],
                'e.entity_id = decimal_index.entity_id' . ' AND ' . $connection->quoteInto(
                    'decimal_index.attribute_id = ?',
                    $attribute->getId()
                ) . ' AND ' . $connection->quoteInto(
                    'decimal_index.store_id = ?',
                    $productCollection->getStoreId()
                ),
                []
            );
            $select->columns(
                [
                    'min' => 'MIN(decimal_index.value)',
                    'max' => 'MAX(decimal_index.value)',
                ]
            );
            $select->where('decimal_index.value IS NOT NULL');
        }
        return $connection->fetchRow($select, [], \Zend_Db::FETCH_NUM);
    }
}
