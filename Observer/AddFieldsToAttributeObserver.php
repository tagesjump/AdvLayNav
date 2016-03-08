<?php
/**
 * Copyright © PART <info@part-online.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Part\AdvLayNav\Observer;

use Magento\Framework\Module\Manager;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Part\AdvLayNav\Model\AdvLayNav;

/**
 * Class AddFieldsToAttributeObserver
 */
class AddFieldsToAttributeObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    /**
     * @param Manager $moduleManager
     */
    public function __construct(Manager $moduleManager)
    {
        $this->moduleManager = $moduleManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        if (!$this->moduleManager->isOutputEnabled('Part_AdvLayNav')) {
            return;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $observer->getForm();
        /** @var \Magento\Framework\Data\Form\Element\AbstractElement $fieldset */
        $fieldset = $form->getElement('front_fieldset');
        $fieldset->addField(
            AdvLayNav::INPUT_TYPE_KEY,
            'select',
            [
                'name' => AdvLayNav::INPUT_TYPE_KEY,
                'label' => __('Show as AdvLayNav Input'),
                'title' => __('Show as AdvLayNav Input'),
                'values' => [
                    AdvLayNav::INPUT_TYPE_NONE => __('No'),
                    AdvLayNav::INPUT_TYPE_RANGE_SLIDER => __('Range Slider'),
                ],
            ],
            'is_filterable'
        );
    }
}
