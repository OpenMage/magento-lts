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
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Rolesedit Tab Display Block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Permissions_Tab_Rolesedit extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Retrieve an instance of the fallback helper
     * @return Mage_Admin_Helper_Rules_Fallback
     */
    protected function _getFallbackHelper()
    {
        return Mage::helper('admin/rules_fallback');
    }

    /**
     * Get tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('adminhtml')->__('Role Resources');
    }

    /**
     * Get tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Whether tab is available
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Whether tab is visible
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Class constructor
     *
     */
    public function __construct()
    {
        parent::__construct();

        $rid = Mage::app()->getRequest()->getParam('rid', false);

        $resources = Mage::getModel('admin/roles')->getResourcesList();

        $rules = Mage::getResourceModel('admin/rules_collection')->getByRoles($rid)->load();

        $selrids = [];

        /** @var Mage_Admin_Model_Rules $item */
        foreach ($rules->getItems() as $item) {
            $itemResourceId = $item->getResource_id();
            if (array_key_exists(strtolower($itemResourceId), $resources)) {
                if ($item->isAllowed()) {
                    $resources[$itemResourceId]['checked'] = true;
                    $selrids[] = $itemResourceId;
                }
            }
        }

        $resourcesPermissionsMap = $rules->getResourcesPermissionsArray();
        $undefinedResources = array_diff(array_keys($resources), array_keys($resourcesPermissionsMap));

        foreach ($undefinedResources as $undefinedResourceId) {
            // Fallback resource permissions
            $permissions = $this->_getFallbackHelper()->fallbackResourcePermissions(
                $resourcesPermissionsMap,
                $undefinedResourceId
            );
            if ($permissions == Mage_Admin_Model_Rules::RULE_PERMISSION_ALLOWED) {
                $selrids[] = $undefinedResourceId;
            }
        }

        $this->setSelectedResources($selrids);

        $this->setTemplate('permissions/rolesedit.phtml');
    }

    /**
     * Check if everything is allowed
     *
     * @return bool
     */
    public function getEverythingAllowed()
    {
        return in_array('all', $this->getSelectedResources());
    }

    /**
     * Get Json Representation of Resource Tree
     *
     * @return string
     */
    public function getResTreeJson()
    {
        $rid = Mage::app()->getRequest()->getParam('rid', false);
        $resources = Mage::getModel('admin/roles')->getResourcesTree();

        $rootArray = $this->_getNodeJson($resources->admin, 1);

        return Mage::helper('core')->jsonEncode($rootArray['children'] ?? []);
    }

    /**
     * Compare two nodes of the Resource Tree
     *
     * @param array $a
     * @param array $b
     * @return int
     */
    protected function _sortTree($a, $b)
    {
        return $a['sort_order'] < $b['sort_order'] ? -1 : ($a['sort_order'] > $b['sort_order'] ? 1 : 0);
    }

    /**
     * Get Node Json
     *
     * @param mixed $node
     * @param int $level
     * @return array
     */
    protected function _getNodeJson($node, $level = 0)
    {
        $item = [];
        $selres = $this->getSelectedResources();

        if ($level != 0) {
            $item['text'] = Mage::helper('adminhtml')->__((string)$node->title);
            $item['sort_order'] = isset($node->sort_order) ? (string)$node->sort_order : 0;
            $item['id'] = (string)$node->attributes()->aclpath;

            if (in_array($item['id'], $selres)) {
                $item['checked'] = true;
            }
        }
        if (isset($node->children)) {
            $children = $node->children->children();
        } else {
            $children = $node->children();
        }
        if (empty($children)) {
            return $item;
        }

        if ($children) {
            $item['children'] = [];
            //$item['cls'] = 'fiche-node';
            foreach ($children as $child) {
                if ($child->getName() != 'title' && $child->getName() != 'sort_order') {
                    if (!(string)$child->title) {
                        continue;
                    }
                    if ($level != 0) {
                        $item['children'][] = $this->_getNodeJson($child, $level + 1);
                    } else {
                        $item = $this->_getNodeJson($child, $level + 1);
                    }
                }
            }
            if (!empty($item['children'])) {
                usort($item['children'], [$this, '_sortTree']);
            }
        }
        return $item;
    }
}
