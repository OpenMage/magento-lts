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
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Api_Tab_Rolesedit extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();

        $rid = Mage::app()->getRequest()->getParam('rid', false);

        $resources = Mage::getModel('api/roles')->getResourcesList();

        $rules = Mage::getResourceModel('api/rules_collection')->getByRoles($rid)->load();

        $selrids = [];

        foreach ($rules->getItems() as $item) {
            if (array_key_exists(strtolower($item->getResource_id()), $resources)
                && $item->getApiPermission() == 'allow'
            ) {
                $resources[$item->getResource_id()]['checked'] = true;
                $selrids[] = $item->getResource_id();
            }
        }

        $this->setSelectedResources($selrids);

        $this->setTemplate('api/rolesedit.phtml');
        //->assign('resources', $resources);
        //->assign('checkedResources', join(',', $selrids));
    }

    public function getEverythingAllowed()
    {
        return in_array('all', $this->getSelectedResources());
    }

    public function getResTreeJson()
    {
        $rid = Mage::app()->getRequest()->getParam('rid', false);
        $resources = Mage::getModel('api/roles')->getResourcesTree();

        $rootArray = $this->_getNodeJson($resources, 1);

        return Mage::helper('core')->jsonEncode($rootArray['children'] ?? []);
    }

    protected function _sortTree($a, $b)
    {
        return $a['sort_order'] <=> $b['sort_order'];
    }

    protected function _getNodeJson($node, $level = 0)
    {
        $item = [];
        $selres = $this->getSelectedResources();

        if ($level != 0) {
            $item['text'] = (string) $node->title;
            $item['sort_order'] = isset($node->sort_order) ? (string) $node->sort_order : 0;
            $item['id']  = (string) $node->attributes()->aclpath;

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
                if ($child->getName() != 'title' && $child->getName() != 'sort_order' && $child->attributes()->module) {
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
