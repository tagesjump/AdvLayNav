<?php
/**
 * Copyright © PART <info@part-online.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 namespace Part\AdvLayNav\Model\Plugin;

/**
 * Class FilterRenderer
 */
class FilterRenderer
{
    /**
     * The layout object.
     *
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    /**
     * The class name of the block for AdvLayNav attributes.
     *
     * @var string
     */
    private $block = 'Part\AdvLayNav\Block\LayeredNavigation\RenderLayered';

    /**
     * The AdvLayNav helper.
     *
     * @var \Part\AdvLayNav\Helper\Data
     */
    private $advLayNavHelper;

    /**
     * Creates a FilterRenderer plugin that will surrounded the render function of a FilterRenderer.
     *
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \Part\AdvLayNav\Helper\Data
     */
    public function __construct(
        \Magento\Framework\View\LayoutInterface $layout,
        \Part\AdvLayNav\Helper\Data $advLayNavHelper
    ) {
        $this->layout = $layout;
        $this->advLayNavHelper = $advLayNavHelper;
    }

    /**
     * Checks if the given filter is for an attribute of type AdvLayNav and renders the attribute with it's own block in
     * that case.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param \Magento\LayeredNavigation\Block\Navigation\FilterRenderer $subject
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\Layer\Filter\FilterInterface $filter
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundRender(
        \Magento\LayeredNavigation\Block\Navigation\FilterRenderer $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\Layer\Filter\FilterInterface $filter
    ) {
        if ($filter->hasAttributeModel()) {
            if ($this->advLayNavHelper->isAdvLayNavRangeSliderAttribute($filter->getAttributeModel())) {
                return $this->layout
                    ->createBlock($this->block)
                    ->setAdvLayNavFilter($filter)
                    ->toHtml();
            }
        }

        return $proceed($filter);
    }
}
