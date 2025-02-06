<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Dataflow
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
