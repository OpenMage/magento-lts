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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Store grid column filter
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Store extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected $_skipAllStoresLabel = false;

    /**
     * Retrieve System Store model
     *
     * @return Mage_Adminhtml_Model_System_Store
     */
    protected function _getStoreModel()
    {
        return Mage::getSingleton('adminhtml/system_store');
    }

    protected function _getShowAllStoresLabelFlag()
    {
        return $this->getColumn()->getData('skipAllStoresLabel')?$this->getColumn()->getData('skipAllStoresLabel'):$this->_skipAllStoresLabel;
    }

    /**
     * Render row store views
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $skipAllStoresLabel = $this->_getShowAllStoresLabelFlag();
        $origStores = $row->getData($this->getColumn()->getIndex());
        $showNumericStores = (bool)$this->getColumn()->getShowNumericStores();
        $stores = array();
        if (!is_array($origStores)) {
            $origStores = array($origStores);
        }
        foreach ($origStores as $origStore) {
            if (is_numeric($origStore)) {
                if (0 == $origStore) {
                    if (!$skipAllStoresLabel) {
                        $stores[] = Mage::helper('adminhtml')->__('All Store Views');
                    }
                }
                elseif ($storeName = $this->_getStoreModel()->getStoreName($origStore)) {
                    if ($this->getColumn()->getStoreView()) {
                        $store = $this->_getStoreModel()->getStoreNameWithWebsite($origStore);
                    } else {
                        $store = $this->_getStoreModel()->getStoreNamePath($origStore);
                    }
                    $layers = array();
                    foreach (explode('/', $store) as $key => $value) {
                        $layers[] = str_repeat("&nbsp;", $key * 3) . $value;
                    }
                    $stores[] = implode('<br/>', $layers);
                }
                elseif ($showNumericStores) {
                    $stores[] = $origStore;
                }
            }
            elseif (is_null($origStore) && $row->getStoreName()) {
                $stores[] = $row->getStoreName() . ' ' . $this->__('[deleted]');
            }
            else {
                $stores[] = $origStore;
            }
        }
        return $stores ? join('<br/> ', $stores) : '&nbsp;';
    }

}
