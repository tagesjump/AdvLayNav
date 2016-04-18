<?php
/**
 * Copyright © PART <info@part-online.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Part\AdvLayNav\Block\LayeredNavigation;

use Magento\Framework\View\Element\Template;

/**
 * Class AbstractRenderLayered
 */
abstract class AbstractRenderLayered extends Template
{
    /**
     * The filter of the RenderLayered.
     *
     * @var \Magento\Catalog\Model\Layer\Filter\AbstractFilter
     */
    protected $filter;

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

        return $this;
    }
}
