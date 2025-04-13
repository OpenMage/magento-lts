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
class Mage_Weee_Model_Observer_UpdateExcludedFieldList extends Mage_Core_Model_Abstract implements Mage_Core_Observer_Interface
{
    /**
     * Exclude WEEE attributes from standard form generation
     */
    public function execute(Varien_Event_Observer $observer): void
    {
        /** @var Mage_Adminhtml_Block_Catalog_Product_Edit_Action_Attribute_Tab_Attributes $block */
        $block = $observer->getEvent()->getDataByKey('object');
        $list = $block->getFormExcludedFieldList();
        $attributes = Mage::getSingleton('weee/tax')->getWeeeAttributeCodes(true);
        $list = array_merge($list, array_values($attributes));
        $block->setFormExcludedFieldList($list);
    }
}
