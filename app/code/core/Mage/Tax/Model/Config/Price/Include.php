<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
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
