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
 * @copyright  Copyright (c) 2021-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Admin system config sturtup page
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Admin_Page
{
    protected $_url;

    public function toOptionArray()
    {
        $options = [];
        $menu    = $this->_buildMenuArray();

        $this->_createOptions($options, $menu);

        return $options;
    }

    protected function _createOptions(&$optionArray, $menuNode)
    {
        $nonEscapableNbspChar = html_entity_decode('&#160;', ENT_NOQUOTES, 'UTF-8');

        foreach ($menuNode as $menu) {
            if (!empty($menu['url'])) {
                $optionArray[] = [
                    'label' => str_repeat($nonEscapableNbspChar, ($menu['level'] * 4)) . $menu['label'],
                    'value' => $menu['path'],
                ];

                if (isset($menu['children'])) {
                    $this->_createOptions($optionArray, $menu['children']);
                }
            } else {
                $children = [];

                if (isset($menu['children'])) {
                    $this->_createOptions($children, $menu['children']);
                }

                $optionArray[] = [
                    'label' => str_repeat($nonEscapableNbspChar, ($menu['level'] * 4)) . $menu['label'],
                    'value' => $children,
                ];
            }
        }
    }

    protected function _getUrlModel()
    {
        if (is_null($this->_url)) {
            $this->_url = Mage::getModel('adminhtml/url');
        }
        return $this->_url;
    }

    protected function _buildMenuArray(?Varien_Simplexml_Element $parent = null, $path = '', $level = 0)
    {
        if (is_null($parent)) {
            $parent = Mage::getSingleton('admin/config')->getAdminhtmlConfig()->getNode('menu');
        }

        $parentArr = [];
        $sortOrder = 0;
        foreach ($parent->children() as $childName => $child) {
            if (($child->disabled == 1)
                || ($child->depends && !$this->_checkDepends($child->depends))
            ) {
                continue;
            }

            $menuArr = [];
            $menuArr['label'] = $this->_getHelperValue($child);

            $menuArr['sort_order'] = $child->sort_order ? (int) $child->sort_order : $sortOrder;

            if ($child->action) {
                $menuArr['url'] = (string) $child->action;
            } else {
                $menuArr['url'] = '';
            }

            $menuArr['level'] = $level;
            $menuArr['path'] = $path . $childName;

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

    protected function _sortMenu($a, $b)
    {
        return $a['sort_order'] < $b['sort_order'] ? -1 : ($a['sort_order'] > $b['sort_order'] ? 1 : 0);
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

    protected function _getHelperValue(Varien_Simplexml_Element $child)
    {
        $helperName         = 'adminhtml';
        $titleNodeName      = 'title';
        $childAttributes    = $child->attributes();
        if (isset($childAttributes['module'])) {
            $helperName     = (string) $childAttributes['module'];
        }

        $titleNodeName = 'title';

        return Mage::helper($helperName)->__((string) $child->$titleNodeName);
    }
}
