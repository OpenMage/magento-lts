<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Dataflow
 */

/**
 * DataFlow Session Model
 *
 * @package    Mage_Dataflow
 *
 * @method Mage_Dataflow_Model_Resource_Session _getResource()
 * @method string                               getComment()
 * @method string                               getCreatedDate()
 * @method string                               getDirection()
 * @method string                               getFile()
 * @method Mage_Dataflow_Model_Resource_Session getResource()
 * @method string                               getType()
 * @method int                                  getUserId()
 * @method $this                                setComment(string $value)
 * @method $this                                setCreatedDate(string $value)
 * @method $this                                setDirection(string $value)
 * @method $this                                setFile(string $value)
 * @method $this                                setType(string $value)
 * @method $this                                setUserId(int $value)
 */
class Mage_Dataflow_Model_Session extends Mage_Core_Model_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('dataflow/session');
    }
}
