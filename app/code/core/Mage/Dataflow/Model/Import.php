<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Dataflow
 */

/**
 * DataFlow Import Model
 *
 * @category   Mage
 * @package    Mage_Dataflow
 *
 * @method Mage_Dataflow_Model_Resource_Import _getResource()
 * @method Mage_Dataflow_Model_Resource_Import getResource()
 * @method int getSessionId()
 * @method $this setSessionId(int $value)
 * @method int getSerialNumber()
 * @method $this setSerialNumber(int $value)
 * @method string getValue()
 * @method $this setValue(string $value)
 * @method int getStatus()
 * @method $this setStatus(int $value)
 */
class Mage_Dataflow_Model_Import extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('dataflow/import');
    }
}
