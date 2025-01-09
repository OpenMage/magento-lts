<?php

/**
 * @category   Mage
 * @package    Mage_Dataflow
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Convert profile interface
 *
 * @category   Mage
 * @package    Mage_Dataflow
 */
interface Mage_Dataflow_Model_Convert_Profile_Interface
{
    /**
     * Run current action
     *
     * @return Mage_Dataflow_Model_Convert_Profile_Abstract
     */
    public function run();
}
