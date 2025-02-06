<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Dataflow
 */

/**
 * Convert action interface
 *
 * @category   Mage
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
