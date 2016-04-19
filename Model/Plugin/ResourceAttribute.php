<?php
/**
 * Copyright © PART <info@part-online.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Part\AdvLayNav\Model\Plugin;

class ResourceAttribute
{
    /**
     * The AdvLayNav helper.
     *
     * @var \Part\AdvLayNav\Helper\Data
     */
    private $advLayNavHelper;

    public function __construct(\Part\AdvLayNav\Helper\Data $advLayNavHelper) {
        $this->advLayNavHelper = $advLayNavHelper;
    }

    public function aroundApplyFilterToCollection(
        \Magento\Catalog\Model\ResourceModel\Layer\Filter\Attribute $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\Layer\Filter\FilterInterface $filter,
        $value
    ) {
        if ($this->advLayNavHelper->isAdvLayNavMultiSelectAttribute($filter->getAttributeModel())) {
            $collection = $filter->getLayer()->getProductCollection();
            $attribute = $filter->getAttributeModel();
            $connection = $subject->getConnection();
            $tableAlias = $attribute->getAttributeCode() . '_idx';
            $orConditions = [];
            foreach ($value as $filterValues) {
                $orConditions[] = $connection->quoteInto("{$tableAlias}.value = ?", $filterValues);
            }
            $andConditions = [
                "{$tableAlias}.entity_id = e.entity_id",
                $connection->quoteInto("{$tableAlias}.attribute_id = ?", $attribute->getAttributeId()),
                $connection->quoteInto("{$tableAlias}.store_id = ?", $collection->getStoreId()),
                ' ( ' . implode(' OR ', $orConditions) . ' ) ',
            ];

            $collection->getSelect()->join(
                [$tableAlias => $subject->getMainTable()],
                implode(' AND ', $andConditions),
                []
            );

            return $subject;
        }
        return $proceed($filter, $value);
    }
}
