<?php
/**
 * Copyright © PART <info@part-online.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Part\AdvLayNav\Block\LayeredNavigation;

/**
 * Class MultiSelect
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class MultiSelect extends AbstractRenderLayered
{
    // @codingStandardsIgnoreStart
    /**
     * The template file for our RenderLayered.
     *
     * @var string
     */
    protected $_template = 'Part_AdvLayNav::product/layered/multiselect.phtml';
    // @codingStandardsIgnoreEnd

    private $htmlPagerBlock;

    public function setHtmlPagerBlock(\Magento\Theme\Block\Html\Pager $htmlPagerBlock)
    {
        $this->htmlPagerBlock = $htmlPagerBlock;

        return $this;
    }

    /**
     * @return array
     */
    public function getFilterItems()
    {
        return $this->filter->getItems();
    }

    public function getFilterItemUrl(\Magento\Catalog\Model\Layer\Filter\Item $item)
    {
        $filter = $item->getFilter();
        $requestParameters = $this->_request->getParam($filter->getRequestVar());
        if (!is_array($requestParameters)) {
            $requestParameters = [$requestParameters];
        }
        $requestParameters[] = $item->getValue();
        $query = [
            $filter->getRequestVar() => array_unique($requestParameters),
            $this->htmlPagerBlock->getPageVarName() => null,
        ];
        return $this->_urlBuilder->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
    }

    public function getFilterItemRemoveUrl(\Magento\Catalog\Model\Layer\Filter\Item $item)
    {
        $filter = $item->getFilter();
        $requestParameters = $this->_request->getParam($filter->getRequestVar());
        if (!is_array($requestParameters)) {
            $requestParameters = [$requestParameters];
        }
        foreach ($requestParameters as $key => $value) {
            if ($value == $item->getValue()) {
                unset($requestParameters[$key]);
            }
        }
        $query = [
            $filter->getRequestVar() => array_unique($requestParameters),
            $this->htmlPagerBlock->getPageVarName() => null,
        ];
        return $this->_urlBuilder->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
    }

    public function isFilterItemActive(\Magento\Catalog\Model\Layer\Filter\Item $item)
    {
        $filter = $item->getFilter();
        $requestParameters = $this->_request->getParam($filter->getRequestVar());
        if (!is_array($requestParameters)) {
            $requestParameters = [$requestParameters];
        }

        return in_array($item->getValue(), $requestParameters);
    }

    public function isFilterActive()
    {
        $requestParameters = $this->_request->getParam($this->filter->getRequestVar());
        return isset($requestParameters);
    }
}
