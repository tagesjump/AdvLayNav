<?php
/**
 * Copyright © PART <info@part-online.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Part\AdvLayNav\Model\Plugin;

class Attribute
{
    /**
     * Resource instance
     *
     * @var \Magento\Catalog\Model\ResourceModel\Layer\Filter\Attribute
     */
    private $resource;

    /**
     * Filter item factory
     *
     * @var \Magento\Catalog\Model\Layer\Filter\ItemFactory
     */
    private $filterItemFactory;

    /**
     * The AdvLayNav helper.
     *
     * @var \Part\AdvLayNav\Helper\Data
     */
    private $advLayNavHelper;

    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Catalog\Model\ResourceModel\Layer\Filter\AttributeFactory $filterAttributeFactory,
        \Part\AdvLayNav\Helper\Data $advLayNavHelper
    ) {
        $this->filterItemFactory = $filterItemFactory;
        $this->resource = $filterAttributeFactory->create();
        $this->advLayNavHelper = $advLayNavHelper;
    }

    public function aroundApply(
        \Magento\CatalogSearch\Model\Layer\Filter\Attribute $subject,
        \Closure $proceed,
        \Magento\Framework\App\RequestInterface $request
    ) {
        if ($this->advLayNavHelper->isAdvLayNavMultiSelectAttribute($subject->getAttributeModel())) {
            $filters = $request->getParam($subject->getRequestVar());
            if (!is_array($filters)) {
                return $this;
            }
            $cleanedTexts = [];
            $cleanedFilters = [];
            foreach ($filters as $key => $filter) {
                $text = $subject->getAttributeModel()->getFrontend()->getOption($filter);
                if ($filter && strlen($text)) {
                    $cleanedTexts[$key] = $text;
                    $cleanedFilters[$key] = $filter;
                }
            }
            $this->getResource()->applyFilterToCollection($subject, $cleanedFilters);
            foreach ($cleanedFilters as $key => $filter) {
                $subject->getLayer()->getState()->addFilter($this->createItem($subject, $cleanedTexts[$key], $filter));
            }
            return $subject;
        }
        return $proceed($request);
    }

    /**
     * Retrieve resource instance
     *
     * @return \Magento\Catalog\Model\ResourceModel\Layer\Filter\Attribute
     */
    private function getResource()
    {
        return $this->resource;
    }

    /**
     * Create filter item object
     *
     * @param   string $label
     * @param   mixed $value
     * @param   int $count
     * @return  \Magento\Catalog\Model\Layer\Filter\Item
     */
    private function createItem(
        \Magento\CatalogSearch\Model\Layer\Filter\Attribute $attribute,
        $label,
        $value,
        $count = 0
    ) {
        return $this->filterItemFactory->create()
            ->setFilter($attribute)
            ->setLabel($label)
            ->setValue($value)
            ->setCount($count);
    }
}
