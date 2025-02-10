<?php
/**
 * Dataflow Batch import resource model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Dataflow
 */
class Mage_Dataflow_Model_Resource_Batch_Import extends Mage_Dataflow_Model_Resource_Batch_Abstract
{
    protected function _construct()
    {
        $this->_init('dataflow/batch_import', 'batch_import_id');
    }
}
