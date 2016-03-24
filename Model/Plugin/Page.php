<?php
/**
 * Copyright © PART <info@part-online.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Part\AdvLayNav\Model\Plugin;

/**
 * Class Page
 */
class Page
{
    /**
     * The request object.
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * The event manager of Magento.
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;

    /**
     * Creates an instance of a Page plugin.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     */
    public function __construct(\Magento\Framework\View\Element\Template\Context $context)
    {
        $this->request = $context->getRequest();
        $this->eventManager = $context->getEventManager();
    }

    /**
     * If parameter advLayNavAjax is given in the request, function render should only return a json with block
     * category.products.list and catalog.leftnav.
     *
     * @param \Magento\Framework\View\Result\Page $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\App\ResponseInterface $response
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @return \Magento\Framework\View\Result\Page
     */
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
