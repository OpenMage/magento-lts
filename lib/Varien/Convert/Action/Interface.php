<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Convert
 */

/**
 * Convert action interface
 *
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
