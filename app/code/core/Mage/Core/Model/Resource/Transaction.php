<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Resource transaction model
 *
 * @package    Mage_Core
 * @todo need collect connection by name
 */
class Mage_Core_Model_Resource_Transaction
{
    /**
     * Objects which will be involved to transaction
     *
     * @var array
     */
    protected $_objects = [];

    /**
     * Transaction objects array with alias key
     *
     * @var array
     */
    protected $_objectsByAlias = [];

    /**
     * Callbacks array.
     *
     * @var array
     */
    protected $_beforeCommitCallbacks = [];

    /**
     * Begin transaction for all involved object resources
     *
     * @return $this
     */
    protected function _startTransaction()
    {
        foreach ($this->_objects as $object) {
            $object->getResource()->beginTransaction();
        }

        return $this;
    }

    /**
     * Commit transaction for all resources
     *
     * @return $this
     */
    protected function _commitTransaction()
    {
        foreach ($this->_objects as $object) {
            $object->getResource()->commit();
        }

        return $this;
    }

    /**
     * Rollback transaction
     *
     * @return $this
     */
    protected function _rollbackTransaction()
    {
        foreach ($this->_objects as $object) {
            $object->getResource()->rollBack();
        }

        return $this;
    }

    /**
     * Run all configured object callbacks
     *
     * @return $this
     */
    protected function _runCallbacks()
    {
        foreach ($this->_beforeCommitCallbacks as $callback) {
            call_user_func($callback);
        }

        return $this;
    }

    /**
     * Adding object for using in transaction
     *
     * @param string $alias
     * @return $this
     */
    public function addObject(Mage_Core_Model_Abstract $object, $alias = '')
    {
        $this->_objects[] = $object;
        if (!empty($alias)) {
            $this->_objectsByAlias[$alias] = $object;
        }

        return $this;
    }

    /**
     * Add callback function which will be called before commit transactions
     *
     * @param callable $callback
     * @return $this
     */
    public function addCommitCallback($callback)
    {
        $this->_beforeCommitCallbacks[] = $callback;
        return $this;
    }

    /**
     * Initialize objects save transaction
     *
     * @return $this
     * @throws Exception
     */
    public function save()
    {
        $this->_startTransaction();
        $error     = false;

        try {
            foreach ($this->_objects as $object) {
                $object->save();
            }
        } catch (Exception $exception) {
            $error = $exception;
        }

        if ($error === false) {
            try {
                $this->_runCallbacks();
            } catch (Exception $exception) {
                $error = $exception;
            }
        }

        if ($error) {
            $this->_rollbackTransaction();
            throw $error;
        } else {
            $this->_commitTransaction();
        }

        return $this;
    }

    /**
     * Initialize objects delete transaction
     *
     * @return $this
     * @throws Exception
     */
    public function delete()
    {
        $this->_startTransaction();
        $error = false;

        try {
            foreach ($this->_objects as $object) {
                $object->delete();
            }
        } catch (Exception $exception) {
            $error = $exception;
        }

        if ($error === false) {
            try {
                $this->_runCallbacks();
            } catch (Exception $exception) {
                $error = $exception;
            }
        }

        if ($error) {
            $this->_rollbackTransaction();
            throw $error;
        } else {
            $this->_commitTransaction();
        }

        return $this;
    }
}
