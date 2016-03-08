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
 * Class AddAdditionalValuesToAttributeObserver
 */
class AddAdditionalValuesToAttributeObserver implements ObserverInterface
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

        /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
        $attribute = $observer->getAttribute();
        $data = $attribute->getData();
        if (isset($data['additional_data'])) {
            $additionalData = unserialize($data['additional_data']);
            if (isset($additionalData[AdvLayNav::INPUT_TYPE_KEY])) {
                $attribute->setData(AdvLayNav::INPUT_TYPE_KEY, $additionalData[AdvLayNav::INPUT_TYPE_KEY]);
            }
        }
    }
}
