<?php
/**
 * Installer data model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Install
 */
class Mage_Install_Model_Installer_Data extends Varien_Object
{
    /**
     * Errors array
     *
     * @var array
     */
    protected $_errors = [];

    /**
     * Add error
     *
     * @param string $error
     * @return $this
     */
    public function addError($error)
    {
        $this->_errors[] = $error;
        return $this;
    }

    /**
     * Get all errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }
}
