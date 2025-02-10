<?php
/**
 * Dataflow Batch export resource model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Dataflow
 */
class Mage_Dataflow_Model_Resource_Batch_Export extends Mage_Dataflow_Model_Resource_Batch_Abstract
{
    protected function _construct()
    {
        $this->_init('dataflow/batch_export', 'batch_export_id');
    }
}
