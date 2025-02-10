<?php
/**
 * Dataflow Batch export model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Dataflow
 * @method Mage_Dataflow_Model_Resource_Batch_Export _getResource()
 * @method Mage_Dataflow_Model_Resource_Batch_Export getResource()
 * @method int getBatchId()
 * @method $this setBatchId(int $value)
 * @method int getStatus()
 * @method $this setStatus(int $value)
 */
class Mage_Dataflow_Model_Batch_Export extends Mage_Dataflow_Model_Batch_Abstract
{
    protected function _construct()
    {
        $this->_init('dataflow/batch_export');
    }
}
