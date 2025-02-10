<?php
/**
 * Convert action interface
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Dataflow
 */
interface Mage_Dataflow_Model_Convert_Action_Interface
{
    /**
     * Run current action
     *
     * @return Mage_Dataflow_Model_Convert_Action_Abstract
     */
    public function run();
}
