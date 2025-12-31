<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Dataflow
 */

/**
 * Dataflow Batch import model
 *
 * @package    Mage_Dataflow
 *
 * @method Mage_Dataflow_Model_Resource_Batch_Import _getResource()
 * @method int                                       getBatchId()
 * @method Mage_Dataflow_Model_Resource_Batch_Import getResource()
 * @method int                                       getStatus()
 * @method $this                                     setBatchId(int $value)
 * @method $this                                     setStatus(int $value)
 */
class Mage_Dataflow_Model_Batch_Import extends Mage_Dataflow_Model_Batch_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('dataflow/batch_import');
    }
}
