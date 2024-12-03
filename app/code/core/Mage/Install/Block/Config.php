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
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Config installation block
 *
 * @category   Mage
 * @package    Mage_Install
 */
class Mage_Install_Block_Config extends Mage_Install_Block_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('install/config.phtml');
    }

    /**
     * Retrieve form data post url
     *
     * @return string
     */
    public function getPostUrl()
    {
        return $this->getUrl('*/*/configPost');
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

    public function getSkipUrlValidation()
    {
        return Mage::getSingleton('install/session')->getSkipUrlValidation();
    }

    public function getSkipBaseUrlValidation()
    {
        return Mage::getSingleton('install/session')->getSkipBaseUrlValidation();
    }

    public function getSessionSaveOptions()
    {
        return [
            'files' => Mage::helper('install')->__('File System'),
            'db'    => Mage::helper('install')->__('Database'),
        ];
    }

    public function getSessionSaveSelect()
    {
        return $this->getLayout()->createBlock('core/html_select')
            ->setName('config[session_save]')
            ->setId('session_save')
            ->setTitle(Mage::helper('install')->__('Save Session Files In'))
            ->setClass('required-entry')
            ->setOptions($this->getSessionSaveOptions())
            ->getHtml();
    }
}
