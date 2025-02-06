<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Dataflow
 */

/**
 * Convert adapter interface
 *
 * @category   Mage
 * @package    Mage_Dataflow
 */
interface Mage_Dataflow_Model_Convert_Adapter_Interface
{
    public function load();

    public function save();
}
