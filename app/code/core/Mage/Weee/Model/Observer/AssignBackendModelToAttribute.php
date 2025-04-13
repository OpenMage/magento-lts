<?php

declare(strict_types=1);

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Weee
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shopping cart operation observer
 *
 * @category   Mage
 * @package    Mage_Weee
 */
class Mage_Weee_Model_Observer_AssignBackendModelToAttribute extends Mage_Core_Model_Abstract implements Mage_Core_Observer_Interface
{
    /**
     * Automatically assign backend model to weee attributes
     */
    public function execute(Varien_Event_Observer $observer): self
    {
        /** @var Mage_Eav_Model_Entity_Attribute_Abstract $object */
        $object = $observer->getEvent()->getDataByKey('attribute');
        if ($object->getFrontendInput() == 'weee') {
            $backendModel = Mage_Weee_Model_Attribute_Backend_Weee_Tax::getBackendModelName();
            $object->setBackendModel($backendModel);
            if (!$object->getApplyTo()) {
                $applyTo = [];
                foreach (Mage_Catalog_Model_Product_Type::getOptions() as $option) {
                    if ($option['value'] == Mage_Catalog_Model_Product_Type::TYPE_GROUPED) {
                        continue;
                    }
                    $applyTo[] = $option['value'];
                }
                $object->setApplyTo($applyTo);
            }
        }

        return $this;
    }
}
