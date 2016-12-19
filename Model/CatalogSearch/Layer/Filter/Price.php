<?php
/**
 * Copyright © PART <info@part-online.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Part\AdvLayNav\Model\CatalogSearch\Layer\Filter;

/**
 * Class Price
 */
class Price extends \Magento\CatalogSearch\Model\Layer\Filter\Price
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $custSession;

    /**
     * @var \Magento\Catalog\Model\Layer\Filter\DataProvider\Price
     */
    private $priceDataProvider;

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
     * @param \Magento\Catalog\Model\ResourceModel\Layer\Filter\Price $resource
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Search\Dynamic\Algorithm $priceAlgorithm
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmFactory $algorithmFactory
     * @param \Magento\Catalog\Model\Layer\Filter\DataProvider\PriceFactory $dataProviderFactory
     * @param \Part\AdvLayNav\Helper\Data $advLayNavHelper
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Catalog\Model\ResourceModel\Layer\Filter\Price $resource,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Search\Dynamic\Algorithm $priceAlgorithm,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmFactory $algorithmFactory,
        \Magento\Catalog\Model\Layer\Filter\DataProvider\PriceFactory $dataProviderFactory,
        \Part\AdvLayNav\Helper\Data $advLayNavHelper,
        array $data = []
    ) {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $resource,
            $customerSession,
            $priceAlgorithm,
            $priceCurrency,
            $algorithmFactory,
            $dataProviderFactory,
            $data
        );
        $this->custSession = $customerSession;
        $this->priceDataProvider = $dataProviderFactory->create(['layer' => $this->getLayer()]);
        $this->advLayNavHelper = $advLayNavHelper;
    }

    /**
     * Apply price range filter
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return $this
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        $attribute = $this->getAttributeModel();
        if ($this->advLayNavHelper->isAdvLayNavRangeSliderAttribute($attribute)) {
            $filter = $request->getParam($this->getRequestVar());
            if (!$filter || is_array($filter)) {
                return $this;
            }

            $filterParams = explode(',', $filter);
            $filter = $this->priceDataProvider->validateFilter($filterParams[0]);
            if (!$filter) {
                return $this;
            }

            list($from, $to) = $filter;
            $this->fromValue = $from;
            $this->toValue = $to;

            $this->getLayer()->getProductCollection()->addFieldToFilter(
                'price',
                ['from' => $from, 'to' =>  empty($to) || $from == $to ? $to : $to - self::PRICE_DELTA]
            );

            $this->getLayer()->getState()->addFilter(
                $this->_createItem($this->_renderRangeLabel(empty($from) ? 0 : $from, $to), $filter)
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
                $productCollection = $layer->getProductCollection();
                if ($this->advLayNavHelper->isFilterApplied($layer->getState(), $attributeCode)) {
                    $productCollection = $layer->getCurrentCategory()->getProductCollection();
                }
                $productCollection->addPriceData(
                    $this->custSession->getCustomerGroupId(),
                    $this->_storeManager->getStore()->getWebsiteId()
                );
                $minValue = $productCollection->getMinPrice();
                $maxValue = $productCollection->getMaxPrice();
                if (!$this->fromValue) {
                    $this->fromValue = $minValue;
                }
                if (!$this->toValue) {
                    $this->toValue = $maxValue;
                }

                if ($minValue == $maxValue) {
                    $this->_items = [];
                } else {
                    $this->_items = [
                        'min' => max(0, floor($minValue)),
                        'from' =>  max(0, $this->fromValue),
                        'to' =>  max(0, $this->toValue),
                        'max' =>  max(0, ceil($maxValue)),
                    ];
                }
            }
            return $this;
        }
        return parent::_initItems();
    }
}
