<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Install
 */

/**
 * Installer data model
 *
 * @category   Mage
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
