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
 * Database config installation block
 *
 * @category   Mage
 * @package    Mage_Install
 */
class Mage_Install_Block_Db_Main extends Mage_Core_Block_Template
{
    /**
     * Array of Database blocks keyed by name
     *
     * @var array
     */
    protected $_databases       = [];

    /**
     * Adding customized database block template for database model type
     *
     * @param  string $type database type
     * @param  string $block database block type
     * @param  string $template
     * @return $this
     */
    public function addDatabaseBlock($type, $block, $template)
    {
        $this->_databases[$type] = [
            'block'     => $block,
            'template'  => $template,
            'instance'  => null,
        ];

        return $this;
    }

    /**
     * Retrieve database block by type
     *
     * @param  string $type database model type
     * @return bool | Mage_Core_Block_Template
     */
    public function getDatabaseBlock($type)
    {
        $block = false;
        if (isset($this->_databases[$type])) {
            if ($this->_databases[$type]['instance']) {
                $block = $this->_databases[$type]['instance'];
            } else {
                $block = $this->getLayout()->createBlock($this->_databases[$type]['block'])
                    ->setTemplate($this->_databases[$type]['template'])
                    ->setIdPrefix($type);
                $this->_databases[$type]['instance'] = $block;
            }
        }
        return $block;
    }

    /**
     * Retrieve database blocks
     *
     * @return array
     */
    public function getDatabaseBlocks()
    {
        $databases = [];
        foreach (array_keys($this->_databases) as $type) {
            $databases[] = $this->getDatabaseBlock($type);
        }
        return $databases;
    }

    /**
     * Retrieve configuration form data object
     *
     * @return Varien_Object
     */
    public function getFormData()
    {
        $data = $this->getData('form_data');
        if (is_null($data)) {
            $data = Mage::getSingleton('install/session')->getConfigData(true);
            if (empty($data)) {
                $data = Mage::getModel('install/installer_config')->getFormData();
            } else {
                $data = new Varien_Object($data);
            }
            $this->setFormData($data);
        }
        return $data;
    }
}
