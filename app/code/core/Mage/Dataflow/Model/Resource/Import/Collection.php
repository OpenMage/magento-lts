<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Dataflow
 */

/**
 * Import collection
 *
 * @package    Mage_Dataflow
 */
class Mage_Dataflow_Model_Resource_Import_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Define resource model and model
     */
    protected function _construct()
    {
        $this->_init('dataflow/import');
    }
}
