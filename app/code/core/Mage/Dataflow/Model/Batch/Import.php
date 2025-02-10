<?php
/**
 * Dataflow Batch import model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Dataflow
 * @method Mage_Dataflow_Model_Resource_Batch_Import _getResource()
 * @method Mage_Dataflow_Model_Resource_Batch_Import getResource()
 * @method int getBatchId()
 * @method $this setBatchId(int $value)
 * @method int getStatus()
 * @method $this setStatus(int $value)
 */
class Mage_Dataflow_Model_Batch_Import extends Mage_Dataflow_Model_Batch_Abstract
{
    protected function _construct()
    {
        $this->_init('dataflow/batch_import');
    }
}
