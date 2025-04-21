<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Dataflow
 */

/**
 * Convert adapter interface
 *
 * @package    Mage_Dataflow
 */
interface Mage_Dataflow_Model_Convert_Adapter_Interface
{
    public function load();

    public function save();
}
