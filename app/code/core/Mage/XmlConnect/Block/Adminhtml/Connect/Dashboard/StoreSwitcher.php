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
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Admin application store switcher renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_StoreSwitcher extends Mage_Adminhtml_Block_Store_Switcher
{
    /**
     * Add sales info to xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $xmlObj
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_StoreSwitcher
     */
    public function addSwitcherToXmlObj(Mage_XmlConnect_Model_Simplexml_Element $xmlObj)
    {
        $websites = $this->getWebsites();
        if ($websites) {
            $this->setUseConfirm(false);

            /** @var $storeSwitcherField Mage_XmlConnect_Model_Simplexml_Form_Element_Custom */
            $storeSwitcherField = Mage::getModel('xmlconnect/simplexml_form_element_custom', array(
                'label' => $this->__('Choose Store View')
            ));
            $storeSwitcherField->setId('store_id');
            $storeSwitcherXmlObj = $storeSwitcherField->toXmlObject();
            $this->_createStoreItemList($websites, $storeSwitcherXmlObj);

            if (!$storeSwitcherXmlObj->getAttribute('value')) {
                $storeSwitcherXmlObj->addAttribute('value', Mage_XmlConnect_Helper_AdminApplication::ALL_STORE_VIEWS);
            }
            $xmlObj->appendChild($storeSwitcherXmlObj);
        }
        return $this;
    }

    /**
     * Create store item list for xml object
     *
     * @param array $websites
     * @param Mage_XmlConnect_Model_Simplexml_Element $storeSwitcherXmlObj
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_StoreSwitcher
     */
    protected function _createStoreItemList($websites, Mage_XmlConnect_Model_Simplexml_Element $storeSwitcherXmlObj)
    {
        $switcherItemsXmlObj = $storeSwitcherXmlObj->addCustomChild('values');

        if ($this->hasDefaultOption()) {
            $this->_addSwitcherItem($switcherItemsXmlObj, Mage_XmlConnect_Helper_AdminApplication::ALL_STORE_VIEWS,
                array('label' => $this->getDefaultStoreName(), 'level' => 1));
        }

        foreach ($websites as $website) {
            foreach ($website->getGroups() as $group) {
                $this->_setStoreItemsByNestingLevel($storeSwitcherXmlObj, $switcherItemsXmlObj, $website, $group);
            }
        }
        return $this;
    }

    /**
     * Set store items to xml object by nesting level
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $storeSwitcherXmlObj
     * @param Mage_XmlConnect_Model_Simplexml_Element $switcherItemsXmlObj
     * @param array $website
     * @param array $group
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_StoreSwitcher
     */
    protected function _setStoreItemsByNestingLevel($storeSwitcherXmlObj, $switcherItemsXmlObj, $website, $group)
    {
        $showWebsite = $showGroup = false;
        foreach ($this->getStores($group) as $store) {
            if ($showWebsite == false) {
                $showWebsite = true;
                $this->_addSwitcherItem($switcherItemsXmlObj, null, array(
                    'label' => $website->getName(), 'level' => 1
                ), true);
            }

            if ($showGroup == false) {
                $showGroup = true;
                $this->_addSwitcherItem($switcherItemsXmlObj, null, array(
                    'label' => $group->getName(), 'level' => 2
                ), true);
            }

            if ($this->getStoreId() == $store->getId()) {
                $storeSwitcherXmlObj->addAttribute('value', $this->getStoreId());
            }

            $this->_addSwitcherItem($switcherItemsXmlObj, $store->getId(), array(
                'label' => $store->getName(), 'level' => 3
            ));
        }
        return $this;
    }

    /**
     * Add switcher item to xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $xmlObj
     * @param string $value
     * @param array $config
     * @param bool $isDisabled
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_StoreSwitcher
     */
    protected function _addSwitcherItem($xmlObj, $value, $config, $isDisabled = false)
    {
        if ($isDisabled) {
            $config += array('disabled' => 1);
        }
        $xmlObj->addCustomChild('item', $value, $config);
        return $this;
    }

    /**
     * Get store switcher params as array
     *
     * @return array
     */
    protected function _getStoreSwitcherParams()
    {
        $result = array();
        $websites =  $this->getWebsites();
        foreach ($websites as $website) {
            $showWebsite = false;
            foreach ($website->getGroups() as $group) {
                $showGroup = false;
                foreach ($this->getStores($group) as $store) {
                    if ($showWebsite == false) {
                        $showWebsite = true;
                        $result[$website->getId()]['name'] = $website->getName();
                        $result[$website->getId()]['store_list'] = array();
                    }

                    if ($showGroup == false) {
                        $showGroup = true;
                        $result[$website->getId()]['store_list'][$group->getId()]['name'] =  $group->getName();
                        $result[$website->getId()]['store_list'][$group->getId()]['view_list'] = array();
                    }
                    $result[$website->getId()]['store_list'][$group->getId()]['view_list'][$store->getId()] = $store
                        ->getName();
                }
            }
        }
        return $result;
    }

    /**
     * Prepare sales data collection
     *
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_SalesInfo
     */
    protected function _prepareLayout()
    {
        $this->registerStoreSwitcher();
        return parent::_prepareLayout();
    }

    /**
     * Set store switcher to registry
     *
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_StoreSwitcher
     */
    public function registerStoreSwitcher()
    {
        Mage::register('store_switcher', $this->_getStoreSwitcherParams());
        return $this;
    }
}
