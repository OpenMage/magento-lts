<?php

/**
 * @category   Varien
 * @package    Varien_Convert
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Convert action interface
 *
 * @category   Varien
 * @package    Varien_Convert
 */
interface Varien_Convert_Action_Interface
{
    /**
     * Run current action
     *
     * @return Varien_Convert_Action_Abstract
     */
    public function run();
}
