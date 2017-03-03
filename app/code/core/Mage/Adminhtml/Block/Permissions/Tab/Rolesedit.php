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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Rolesedit Tab Display Block
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Permissions_Tab_Rolesedit extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
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

        $rules_set = Mage::getResourceModel('admin/rules_collection')->getByRoles($rid)->load();

        $selrids = array();

        /** @var $item Mage_Admin_Model_Rules */
        foreach ($rules_set->getItems() as $item) {
            $itemResourceId = $item->getResource_id();
            if (array_key_exists(strtolower($itemResourceId), $resources)) {
                if ($item->isAllowed()) {
                    $resources[$itemResourceId]['checked'] = true;
                    array_push($selrids, $itemResourceId);
                }
            }
        }

        $resourcesPermissionsMap = $rules_set->getResourcesPermissionsArray();
        $undefinedResources = array_diff(array_keys($resources), array_keys($resourcesPermissionsMap));

        foreach ($undefinedResources as $undefinedResourceId) {
            if ($this->_getFallbackHelper()->fallbackResourcePermissions(
                    $resourcesPermissionsMap,
                    $undefinedResourceId
                ) == Mage_Admin_Model_Rules::RULE_PERMISSION_ALLOWED
            ) {
                array_push($selrids, $undefinedResourceId);
            }
        }


        $this->setSelectedResources($selrids);

        $this->setTemplate('permissions/rolesedit.phtml');
        //->assign('resources', $resources);
        //->assign('checkedResources', join(',', $selrids));
    }

    /**
     * Check if everything is allowed
     *
     * @return boolean
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

        $json = Mage::helper('core')->jsonEncode(isset($rootArray['children']) ? $rootArray['children'] : array());

        return $json;
    }

    /**
     * Compare two nodes of the Resource Tree
     *
     * @param array $a
     * @param array $b
     * @return boolean
     */
    protected function _sortTree($a, $b)
    {
        return $a['sort_order']<$b['sort_order'] ? -1 : ($a['sort_order']>$b['sort_order'] ? 1 : 0);
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
        $item = array();
        $selres = $this->getSelectedResources();

        if ($level != 0) {
            $item['text'] = Mage::helper('adminhtml')->__((string)$node->title);
            $item['sort_order'] = isset($node->sort_order) ? (string)$node->sort_order : 0;
            $item['id'] = (string)$node->attributes()->aclpath;

            if (in_array($item['id'], $selres))
                $item['checked'] = true;
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
            $item['children'] = array();
            //$item['cls'] = 'fiche-node';
            foreach ($children as $child) {
                if ($child->getName() != 'title' && $child->getName() != 'sort_order') {
                    if (!(string)$child->title) {
                        continue;
                    }
                    if ($level != 0) {
                        $item['children'][] = $this->_getNodeJson($child, $level+1);
                    } else {
                        $item = $this->_getNodeJson($child, $level+1);
                    }
                }
            }
            if (!empty($item['children'])) {
                usort($item['children'], array($this, '_sortTree'));
            }
        }
        return $item;
    }
}
