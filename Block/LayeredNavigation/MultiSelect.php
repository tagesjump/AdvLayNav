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

    /**
     * @return array
     */
    public function getFilterItems()
    {
        return $this->filter->getItems();
    }
}
