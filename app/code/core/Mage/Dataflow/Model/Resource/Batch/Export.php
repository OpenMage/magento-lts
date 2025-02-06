<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Dataflow
 */

/**
 * Dataflow Batch export resource model
 *
 * @category   Mage
 * @package    Mage_Dataflow
 */
class Mage_Dataflow_Model_Resource_Batch_Export extends Mage_Dataflow_Model_Resource_Batch_Abstract
{
    protected function _construct()
    {
        $this->_init('dataflow/batch_export', 'batch_export_id');
    }
}
