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
        \Part\AdvLayNav\Helper\Data $advLayNavHelper
    ) {
        $this->filterItemFactory = $filterItemFactory;
        $this->advLayNavHelper = $advLayNavHelper;
    }

    public function aroundApply(
        \Magento\CatalogSearch\Model\Layer\Filter\Attribute $subject,
        \Closure $proceed,
        \Magento\Framework\App\RequestInterface $request
    ) {
        if ($this->advLayNavHelper->isAdvLayNavMultiSelectAttribute($subject->getAttributeModel())) {
            $attributeValue = $request->getParam($subject->getRequestVar());
            if (empty($attributeValue) || !is_array($attributeValue)) {
                return $this;
            }
            $attribute = $subject->getAttributeModel();
            $productCollection = $subject->getLayer()->getProductCollection();
            $productCollection->addFieldToFilter($attribute->getAttributeCode(), ['in' => $attributeValue]);
            foreach ($attributeValue as $value) {
                $label = $attribute->getFrontend()->getOption($value);
                $subject->getLayer()->getState()->addFilter($this->createItem($subject, $label, $value));
            }
            return $subject;
        }
        return $proceed($request);
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
