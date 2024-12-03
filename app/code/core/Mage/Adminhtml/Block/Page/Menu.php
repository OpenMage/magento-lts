<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml menu block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 *
 * @method $this setAdditionalCacheKeyInfo(array $cacheKeyInfo)
 * @method array getAdditionalCacheKeyInfo()
 */
class Mage_Adminhtml_Block_Page_Menu extends Mage_Adminhtml_Block_Template
{
    public const CACHE_TAGS = 'BACKEND_MAINMENU';

    /**
     * Adminhtml URL instance
     *
     * @var Mage_Adminhtml_Model_Url
     */
    protected $_url;

    /**
     * Initialize template and cache settings
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('page/menu.phtml');
        $this->_url = Mage::getModel('adminhtml/url');
        $this->setCacheTags([self::CACHE_TAGS]);
    }

    /**
     * Retrieve cache lifetime
     *
     * @return int
     */
    public function getCacheLifetime()
    {
        return 86400;
    }

    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $cacheKeyInfo = [
            'admin_top_nav',
            $this->getActive(),
            Mage::getSingleton('admin/session')->getUser()->getId(),
            Mage::app()->getLocale()->getLocaleCode()
        ];
        // Add additional key parameters if needed
        $additionalCacheKeyInfo = $this->getAdditionalCacheKeyInfo();
        if (is_array($additionalCacheKeyInfo) && !empty($additionalCacheKeyInfo)) {
            $cacheKeyInfo = array_merge($cacheKeyInfo, $additionalCacheKeyInfo);
        }
        return $cacheKeyInfo;
    }

    /**
     * Retrieve Adminhtml Menu array
     *
     * @return array
     */
    public function getMenuArray()
    {
        $parent = Mage::getSingleton('admin/config')->getAdminhtmlConfig()->getNode('menu');

        return $this->_buildMenuArray($parent);
    }

    /**
     * Retrieve Title value for menu node
     *
     * @return string
     */
    protected function _getHelperValue(Varien_Simplexml_Element $child)
    {
        $helperName         = 'adminhtml';
        $titleNodeName      = 'title';
        $childAttributes    = $child->attributes();
        if (isset($childAttributes['module'])) {
            $helperName     = (string)$childAttributes['module'];
        }

        return Mage::helper($helperName)->__((string)$child->$titleNodeName);
    }

    /**
     * Recursive Build Menu array
     *
     * @param string $path
     * @param int $level
     * @return array
     */
    protected function _buildMenuArray(Varien_Simplexml_Element $parent, $path = '', $level = 0)
    {
        $parentArr = [];
        $sortOrder = 0;
        foreach ($parent->children() as $childName => $child) {
            if ($child->disabled == 1) {
                continue;
            }

            $aclResource = 'admin/' . ($child->resource ? (string)$child->resource : $path . $childName);
            if (!$this->_checkAcl($aclResource) || !$this->_isEnabledModuleOutput($child)) {
                continue;
            }

            if ($child->depends && !$this->_checkDepends($child->depends)) {
                continue;
            }

            $menuArr = [];

            $menuArr['label'] = $this->_getHelperValue($child);

            $menuArr['sort_order'] = $child->sort_order ? (int)$child->sort_order : $sortOrder;

            if ($child->action) {
                $menuArr['url'] = $this->_url->getUrl((string)$child->action, ['_cache_secret_key' => true]);
            } else {
                $menuArr['url'] = '#';
                $menuArr['click'] = 'return false';
            }

            $menuArr['active'] = ($this->getActive() == $path . $childName)
                || (strpos((string)$this->getActive(), $path . $childName . '/') === 0);

            $menuArr['level'] = $level;

            if ($child->target) {
                $menuArr['target'] = $child->target;
            }

            if ($child->children) {
                $menuArr['children'] = $this->_buildMenuArray($child->children, $path . $childName . '/', $level + 1);
            }
            $parentArr[$childName] = $menuArr;

            $sortOrder++;
        }

        uasort($parentArr, [$this, '_sortMenu']);

        $last = array_key_last($parentArr);
        if (!is_null($last)) {
            $parentArr[$last]['last'] = true;
        }

        return $parentArr;
    }

    /**
     * Sort menu comparison function
     *
     * @param array $a
     * @param array $b
     * @return int
     */
    protected function _sortMenu($a, $b)
    {
        return $a['sort_order'] < $b['sort_order'] ? -1 : ($a['sort_order'] > $b['sort_order'] ? 1 : 0);
    }

    /**
     * Check Depends
     *
     * @return bool
     */
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

        if ($depends->config) {
            foreach ($depends->config as $path) {
                if (!Mage::getStoreConfigFlag((string)$path)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check is Allow menu item for admin user
     *
     * @param string $resource
     * @return bool
     */
    protected function _checkAcl($resource)
    {
        try {
            $res =  Mage::getSingleton('admin/session')->isAllowed($resource);
        } catch (Exception $e) {
            return false;
        }
        return $res;
    }

    /**
     * Processing block html after rendering
     *
     * @param   string $html
     * @return  string
     */
    protected function _afterToHtml($html)
    {
        $html = preg_replace_callback('#' . Mage_Adminhtml_Model_Url::SECRET_KEY_PARAM_NAME . '/\$([^\/].*)/([^\$].*)\$#', [$this, '_callbackSecretKey'], $html);

        return $html;
    }

    /**
     * Replace Callback Secret Key
     *
     * @param array $match
     * @return string
     */
    protected function _callbackSecretKey($match)
    {
        return Mage_Adminhtml_Model_Url::SECRET_KEY_PARAM_NAME . '/'
            . $this->_url->getSecretKey($match[1], $match[2]);
    }

    /**
     * Get menu level HTML code
     *
     * @param array $menu
     * @param int $level
     * @return string
     */
    public function getMenuLevel($menu, $level = 0)
    {
        $html = '<ul ' . (!$level ? 'id="nav"' : '') . '>' . PHP_EOL;
        foreach ($menu as $item) {
            if ((empty($item['url']) || ($item['url'] == '#')) && empty($item['children'])) {
                continue; // for example hide System/Tools when empty
            }
            $html .= '<li ' . (!empty($item['children']) ? 'onmouseover="Element.addClassName(this,\'over\')" '
                . 'onmouseout="Element.removeClassName(this,\'over\')"' : '') . ' class="'
                . (!$level && !empty($item['active']) ? ' active' : '') . ' '
                . (!empty($item['children']) ? ' parent' : '')
                . (!empty($level) && !empty($item['last']) ? ' last' : '')
                . ' level' . $level . '"> <a href="' . $item['url'] . '" '
                . (!empty($item['title']) ? 'title="' . $item['title'] . '"' : '') . ' '
                . (!empty($item['target']) ? 'target="' . $item['target'] . '"' : '') . ' '
                . (!empty($item['click']) ? 'onclick="' . $item['click'] . '"' : '') . ' class="'
                . (!empty($item['active']) ? 'active' : '') . '"><span>'
                . $this->escapeHtml($item['label']) . '</span></a>' . PHP_EOL;
            if (!empty($item['children'])) {
                $html .= $this->getMenuLevel($item['children'], $level + 1);
            }
            $html .= '</li>' . PHP_EOL;
        }
        $html .= '</ul>' . PHP_EOL;

        return $html;
    }

    /**
     * Check is module output enabled
     *
     * @return bool
     */
    protected function _isEnabledModuleOutput(Varien_Simplexml_Element $child)
    {
        $helperName      = 'adminhtml';
        $childAttributes = $child->attributes();
        if (isset($childAttributes['module'])) {
            $helperName  = (string)$childAttributes['module'];
        }

        return Mage::helper($helperName)->isModuleOutputEnabled();
    }
}
