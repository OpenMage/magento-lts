<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Dataflow
 */

/**
 * DataFlow Session Resource Model
 *
 * @package    Mage_Dataflow
 */
class Mage_Dataflow_Model_Resource_Session extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('dataflow/session', 'session_id');
    }
}
