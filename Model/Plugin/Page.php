<?php
/**
 * Copyright © PART <info@part-online.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Part\AdvLayNav\Model\Plugin;

class Page
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context)
    {
        $this->request = $context->getRequest();
        $this->eventManager = $context->getEventManager();
    }

    public function aroundRenderResult(
        \Magento\Framework\View\Result\Page $subject,
        \Closure $proceed,
        \Magento\Framework\App\ResponseInterface $response
    ) {
        if ($this->request->getParam('advLayNavAjax')) {
            \Magento\Framework\Profiler::start('LAYOUT');
            \Magento\Framework\Profiler::start('layout_render');

            /** @var Magento\Framework\View\Layout $layout */
            $layout = $subject->getLayout();
            $productListBlock = $layout->getBlock('category.products.list');
            $leftnavBlock = $layout->getBlock('catalog.leftnav');
            $data = [
                str_replace(['&advLayNavAjax=1', '&amp;advLayNavAjax=1'], '', trim($productListBlock->toHtml())),
                str_replace(['&advLayNavAjax=1', '&amp;advLayNavAjax=1'], '', trim($leftnavBlock->toHtml())),
            ];
            $response->appendBody(json_encode($data));

            $this->eventManager->dispatch('layout_render_before');
            $this->eventManager->dispatch('layout_render_before_' . $this->request->getFullActionName());
            \Magento\Framework\Profiler::stop('layout_render');
            \Magento\Framework\Profiler::stop('LAYOUT');

            return $subject;
        }

        return $proceed($response);
    }
}
