<?php
/**
 * Copyright © PART <info@part-online.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Part\AdvLayNav\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class ProcessLayoutRenderElementObserver
 */
class ProcessLayoutRenderElementObserver implements ObserverInterface
{
    /**
     * @var array
     */
    private $blockNames = [
        'product_list' => [
            'category.products.list',
            'search.result',
        ],
        'navigation' => [
            'catalog.leftnav',
            'catalogsearch.leftnav',
        ],
    ];

    /**
     * Surrounds the content of the blocks category.products.list and catalog.leftnav with span elements to find them
     * later in the DOM.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $name = $observer->getElementName();
        foreach ($this->blockNames as $blockType => $blockNames) {
            if (in_array($name, $blockNames)) {
                $this->addBeforeAndAfterTagToTransport($observer->getTransport(), $blockType, $name);
                break;
            }
        }
    }

    private function addBeforeAndAfterTagToTransport(\Magento\Framework\DataObject $transport, $blockType, $blockName)
    {
        $output = $transport->getData('output');
        $output = sprintf(
            '<span id="advlaynav_%1$s_before" data-block-name="%2$s"></span>%3$s<span id="advlaynav_%1$s_after">'
                .'</span>',
            $blockType,
            urlencode($blockName),
            $output
        );
        $transport->setData('output', $output);
    }
}
