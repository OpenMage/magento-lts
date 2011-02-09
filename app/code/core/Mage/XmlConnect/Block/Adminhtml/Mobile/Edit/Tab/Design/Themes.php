<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Design_Themes extends Mage_Adminhtml_Block_Template
{
    /**
     * Set themes template
     * Set color fieldsets
     */
    public function __construct()
    {
        parent::__construct();

        $model = Mage::registry('current_app');
        $this->setTemplate('xmlconnect/form/element/themes.phtml');

        $data = $model->getFormData();
        $this->setColorFieldset (array (
            array ( 'id' => 'field_colors', 'label' =>   Mage::helper('xmlconnect')->__('Colors'), 'fields' => array (
                $this->_addColorBox('conf[native][navigationBar][tintColor]', Mage::helper('xmlconnect')->__('Header Background Color'), $data),
                $this->_addColorBox('conf[native][body][primaryColor]', Mage::helper('xmlconnect')->__('Primary Color'), $data),
                $this->_addColorBox('conf[native][body][secondaryColor]', Mage::helper('xmlconnect')->__('Secondary Color'), $data),
                $this->_addColorBox('conf[native][categoryItem][backgroundColor]', Mage::helper('xmlconnect')->__('Category Item Background Color'), $data),
                $this->_addColorBox('conf[native][categoryItem][tintColor]', Mage::helper('xmlconnect')->__('Category Button Color'), $data),
            )),
            array ( 'id' => 'field_fonts', 'label' =>   Mage::helper('xmlconnect')->__('Fonts'), 'fields' => array (
                $this->_addColorBox('conf[extra][fontColors][header]', Mage::helper('xmlconnect')->__('Header Font Color'), $data),
                $this->_addColorBox('conf[extra][fontColors][primary]', Mage::helper('xmlconnect')->__('Primary Font Color'), $data),
                $this->_addColorBox('conf[extra][fontColors][secondary]', Mage::helper('xmlconnect')->__('Secondary Font Color'), $data),
                $this->_addColorBox('conf[extra][fontColors][price]', Mage::helper('xmlconnect')->__('Price Font Color'), $data),
            )),
            array ( 'id' => 'field_advanced', 'label' =>  Mage::helper('xmlconnect')->__('Advanced Settings'), 'fields' => array (
                $this->_addColorBox('conf[native][body][backgroundColor]', Mage::helper('xmlconnect')->__('Background Color'), $data),
                $this->_addColorBox('conf[native][body][scrollBackgroundColor]', Mage::helper('xmlconnect')->__('Scroll Background Color'), $data),
                $this->_addColorBox('conf[native][itemActions][relatedProductBackgroundColor]', Mage::helper('xmlconnect')->__('Related Product Background Color'), $data),
            )),
        ));
    }

    /**
     * Themes array getter
     *
     * @return array
     */
    public function getAllThemes()
    {
        $result = array();
        foreach ($this->getThemes() as $theme) {
            $result[$theme->getName()] = $theme->getFormData();
        }
        return $result;
    }

    /**
     * Create color field params
     *
     * @param id $id
     * @param string $label
     * @param array $data
     * @return array
     */
    protected function _addColorBox($id, $label, $data)
    {
        return array(
            'id'    => $id,
            'name'  => $id,
            'label' => $label,
            'value' => isset($data[$id]) ? $data[$id] : ''
        );
    }

    /**
     * Getter, check if it's needed to load default theme config
     *
     * @return bool
     */
    public function getDefaultThemeLoaded()
    {
        return $this->getApplication()->getDefaultThemeLoaded();
    }

    /**
     * Check if adding new Application
     *
     * @return bool
     */
    public function isNewApplication()
    {
        return $this->getApplication()->getId() ? false : true;
    }

    /**
     * Save theme action url getter
     *
     * @return string
     */
    public function getSaveThemeActionUrl()
    {
        return $this->getUrl('*/*/saveTheme');
    }

    /**
     * Reset theme action url getter
     *
     * @return string
     */
    public function getResetThemeActionUrl()
    {
        return $this->getUrl('*/*/resetTheme');
    }

    /**
     * Getter for current loaded application model
     *
     * @return Mage_XmlConnect_Model_Application
     */
    public function getApplication()
    {
        $model = Mage::registry('current_app');
        if (!($model instanceof Mage_XmlConnect_Model_Application)) {
            Mage::throwException(Mage::helper('xmlconnect')->__('App model not loaded.'));
        }

        return $model;
    }
}
