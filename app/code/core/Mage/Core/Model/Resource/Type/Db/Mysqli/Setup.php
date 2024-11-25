<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Model_Resource_Type_Db_Mysqli_Setup extends Mage_Core_Model_Resource_Type_Db
{
    /**
     * Get connection
     *
     * @param array $config
     * @return Varien_Db_Adapter_Mysqli
     */
    public function getConnection($config)
    {
        return new Varien_Db_Adapter_Mysqli((array) $config);
    }
}
