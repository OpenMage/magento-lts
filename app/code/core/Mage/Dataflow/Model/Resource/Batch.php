<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Dataflow
 */

/**
 * Dataflow Batch resource model
 *
 * @package    Mage_Dataflow
 */
class Mage_Dataflow_Model_Resource_Batch extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('dataflow/batch', 'batch_id');
    }
}
