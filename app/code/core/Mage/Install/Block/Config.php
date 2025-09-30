<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Install
 */

/**
 * Config installation block
 *
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
