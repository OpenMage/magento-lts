<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Dataflow
 */

/**
 * DataFlow Import Model
 *
 * @package    Mage_Dataflow
 *
 * @method Mage_Dataflow_Model_Resource_Import            _getResource()
 * @method Mage_Dataflow_Model_Resource_Import_Collection getCollection()
 * @method Mage_Dataflow_Model_Resource_Import            getResource()
 * @method Mage_Dataflow_Model_Resource_Import_Collection getResourceCollection()
 * @method int                                            getSerialNumber()
 * @method int                                            getSessionId()
 * @method int                                            getStatus()
 * @method string                                         getValue()
 * @method $this                                          setSerialNumber(int $value)
 * @method $this                                          setSessionId(int $value)
 * @method $this                                          setStatus(int $value)
 * @method $this                                          setValue(string $value)
 */
class Mage_Dataflow_Model_Import extends Mage_Core_Model_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('dataflow/import');
    }
}
