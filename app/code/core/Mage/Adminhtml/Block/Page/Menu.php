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
 * Adminhtml menu block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Page_Menu extends Mage_Adminhtml_Block_Template
{
    protected $_url;
    const CACHE_TAGS = 'BACKEND_MAINMENU';

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('page/menu.phtml');
        $this->_url = Mage::getModel('adminhtml/url');
        $this->setCacheTags(array(self::CACHE_TAGS));
    }

    public function getCacheLifetime()
    {
        return 86400;
    }

    public function getCacheKey()
    {
        // getting roles for current user, for now one role per user
        $roles = implode('', Mage::getSingleton('admin/session')->getUser()->getRoles());
        return 'admin_top_nav_'.$this->getActive().'_'.$roles.'_'.Mage::app()->getLocale()->getLocaleCode();
    }

    public function getMenuArray()
    {
        return $this->_buildMenuArray();
    }

    protected function _getHelperValue(Varien_Simplexml_Element $child)
    {
        $helperName         = 'adminhtml';
        $titleNodeName      = 'title';
        $childAttributes    = $child->attributes();
        if (isset($childAttributes['module'])) {
            $helperName     = (string)$childAttributes['module'];
        }
//        if (isset($childAttributes['translate'])) {
//            $titleNodeName  = (string)$childAttributes['translate'];
//        }

        return Mage::helper($helperName)->__((string)$child->$titleNodeName);
    }

    protected function _buildMenuArray(Varien_Simplexml_Element $parent=null, $path='', $level=0)
    {
        if (is_null($parent)) {
            $parent = Mage::getConfig()->getNode('adminhtml/menu');
//        $parent = Mage::getSingleton('adminhtml/config')->getNode('admin/menu');

        }
        $parentArr = array();
        $sortOrder = 0;
        foreach ($parent->children() as $childName=>$child) {

            $aclResource = 'admin/'.$path.$childName;
            if (!$this->_checkAcl($aclResource)) {
                continue;
            }

            if ($child->depends && !$this->_checkDepends($child->depends)) {
                continue;
            }

            $menuArr = array();

            $menuArr['label'] = $this->_getHelperValue($child);

            $menuArr['sort_order'] = $child->sort_order ? (int)$child->sort_order : $sortOrder;

            if ($child->action) {
                $menuArr['url'] = $this->_url->getUrl((string)$child->action);
            } else {
                $menuArr['url'] = '#';
                $menuArr['click'] = 'return false';
            }
            #print_r($this->getActive().','.$path.$childName."<hr>");
            $menuArr['active'] = ($this->getActive()==$path.$childName)
                || (strpos($this->getActive(), $path.$childName.'/')===0);

            $menuArr['level'] = $level;

            if ($child->children) {
                $menuArr['children'] = $this->_buildMenuArray($child->children, $path.$childName.'/', $level+1);
            }
            $parentArr[$childName] = $menuArr;

            $sortOrder++;
        }

        uasort($parentArr, array($this, '_sortMenu'));

        while (list($key, $value) = each($parentArr)) {
            $last = $key;
        }
        if (isset($last)) {
            $parentArr[$last]['last'] = true;
        }

        return $parentArr;
    }

    protected function _sortMenu($a, $b)
    {
        return $a['sort_order']<$b['sort_order'] ? -1 : ($a['sort_order']>$b['sort_order'] ? 1 : 0);
    }

    protected function _checkDepends(Varien_Simplexml_Element $depends)
    {
        if ($depends->module) {
            $modulesConfig = Mage::getConfig()->getNode('modules');
            foreach ($depends->module as $module) {
                if (!$modulesConfig->$module || !$modulesConfig->$module->is('active')) {
                    return false;
                }
            }
        }

        return true;
    }

    /*protected function _checkAcl(Varien_Simplexml_Element $acl)
    {
        return true;
        $resource = (string)$acl->resource;
        $privilege = (string)$acl->privilege;
        return Mage::getSingleton('admin/session')->isAllowed($resource, $privilege);
    }*/

    protected function _checkAcl($resource)
    {
        try {
            $res =  Mage::getSingleton('admin/session')->isAllowed($resource);
        } catch (Exception $e) {
            return false;
        }
        return $res;
    }
}
