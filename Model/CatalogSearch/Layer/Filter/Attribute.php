<?php
/**
 * Copyright © PART <info@part-online.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Part\AdvLayNav\Model\CatalogSearch\Layer\Filter;

/**
 * Class Attribute
 */
class Attribute extends \Magento\CatalogSearch\Model\Layer\Filter\Attribute
{
    /**
     * The AdvLayNav helper.
     *
     * @var \Part\AdvLayNav\Helper\Data
     */
    private $advLayNavHelper;

    /**
     * @var \Magento\Framework\Filter\StripTags
     */
    private $myTagFilter;

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Layer $layer
     * @param \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder
     * @param \Magento\Framework\Filter\StripTags $tagFilter
     * @param \Part\AdvLayNav\Helper\Data
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Framework\Filter\StripTags $tagFilter,
        \Part\AdvLayNav\Helper\Data $advLayNavHelper,
        array $data = []
    ) {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $tagFilter,
            $data
        );
        $this->myTagFilter = $tagFilter;
        $this->advLayNavHelper = $advLayNavHelper;
    }

    /**
     * Apply attribute option filter to product collection
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        $attribute = $this->getAttributeModel();
        if ($this->advLayNavHelper->isAdvLayNavMultiSelectAttribute($attribute)) {
            $attributeValues = $request->getParam($this->_requestVar);
            if (empty($attributeValues) || !is_array($attributeValues)) {
                return $this;
            }
            $productCollection = $this->getLayer()->getProductCollection();
            $productCollection->addFieldToFilter($attribute->getAttributeCode(), ['in' => $attributeValues]);
            foreach ($attributeValues as $attributeValue) {
                $label = $this->getOptionText($attributeValue);
                $this->getLayer()->getState()->addFilter($this->_createItem($label, $attributeValue));
            }
            return $this;
        }
        return parent::apply($request);
    }

    /**
     * Get data array for building attribute filter items
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getItemsData()
    {
        $attribute = $this->getAttributeModel();
        if ($this->advLayNavHelper->isAdvLayNavMultiSelectAttribute($attribute)) {
            $attributeCode = $attribute->getAttributeCode();
            /** @var \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $productCollection */
            $productCollection = $this->getLayer()->getProductCollection();
            $productCollection->addFieldToFilter($attributeCode, []);
            $optionsFacetedData = $productCollection->getFacetedData($attributeCode);

            $productSize = $productCollection->getSize();

            $options = $attribute->getFrontend()
                ->getSelectOptions();
            foreach ($options as $option) {
                if (empty($option['value'])) {
                    continue;
                }
                // Check filter type
                if (empty($optionsFacetedData[$option['value']]['count'])
                    || ($this->getAttributeIsFilterable($attribute) == static::ATTRIBUTE_OPTIONS_ONLY_WITH_RESULTS
                        && !$this->isOptionReducesResults($optionsFacetedData[$option['value']]['count'], $productSize))
                ) {
                    continue;
                }
                $this->itemDataBuilder->addItemData(
                    $this->tagFilter->filter($option['label']),
                    $option['value'],
                    $optionsFacetedData[$option['value']]['count']
                );
            }
            $result = $this->itemDataBuilder->build();
            // $productCollection->addFieldToFilter($attributeCode, $filter);

            return $result;
        }
        return parent::_getItemsData();
    }
}
