<?php

/**
 * @category   Mage
 * @package    Mage_Dataflow
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * DataFlow Session Model
 *
 * @category   Mage
 * @package    Mage_Dataflow
 *
 * @method Mage_Dataflow_Model_Resource_Session _getResource()
 * @method Mage_Dataflow_Model_Resource_Session getResource()
 * @method int getUserId()
 * @method $this setUserId(int $value)
 * @method string getCreatedDate()
 * @method $this setCreatedDate(string $value)
 * @method string getFile()
 * @method $this setFile(string $value)
 * @method string getType()
 * @method $this setType(string $value)
 * @method string getDirection()
 * @method $this setDirection(string $value)
 * @method string getComment()
 * @method $this setComment(string $value)
 */
class Mage_Dataflow_Model_Session extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('dataflow/session');
    }
}
