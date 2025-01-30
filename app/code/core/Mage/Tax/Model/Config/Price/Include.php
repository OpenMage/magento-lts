<?php

/**
 * @category   Mage
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Tax
 */
class Mage_Tax_Model_Config_Price_Include extends Mage_Core_Model_Config_Data
{
    public function afterSave()
    {
        parent::afterSave();
        Mage::app()->cleanCache('checkout_quote');
    }
}
