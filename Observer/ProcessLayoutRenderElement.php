<?php
/**
 * Copyright © PART <info@part-online.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Part\AdvLayNav\Observer;

use Magento\Framework\Event\ObserverInterface;

class ProcessLayoutRenderElement implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $event = $observer->getEvent();
        $name = $event->getElementName();
        if ($name === 'category.products.list' || $name === 'catalog.leftnav') {
            $transport = $event->getTransport();
            $output = $transport->getData('output');
            $output = sprintf(
                '<span id="advlaynav_%1$s_before"></span>%2$s<span id="advlaynav_%1$s_after"></span>',
                $name,
                $output
            );
            $transport->setData('output', $output);
        }
    }
}
