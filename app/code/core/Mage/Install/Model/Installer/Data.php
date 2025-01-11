<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Install
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
