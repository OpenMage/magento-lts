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
 * Categories tree with checkboxes
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Category_Checkboxes_Tree extends Mage_Adminhtml_Block_Catalog_Category_Tree
{
    protected $_selectedIds = [];

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->setTemplate('catalog/category/checkboxes/tree.phtml');
        return $this;
    }

    public function getCategoryIds()
    {
        return $this->_selectedIds;
    }

    public function setCategoryIds($ids)
    {
        if (empty($ids)) {
            $ids = [];
        } elseif (!is_array($ids)) {
            $ids = [(int) $ids];
        }
        $this->_selectedIds = $ids;
        return $this;
    }

    protected function _getNodeJson($node, $level = 1)
    {
        $item = [];
        $item['text'] = $this->escapeHtml($node->getName());

        if ($this->_withProductCount) {
            $item['text'] .= ' (' . $node->getProductCount() . ')';
        }
        $item['id']  = $node->getId();
        $item['path'] = $node->getData('path');
        $item['cls'] = 'folder ' . ($node->getIsActive() ? 'active-category' : 'no-active-category');
        $item['allowDrop'] = false;
        $item['allowDrag'] = false;

        if ($node->hasChildren()) {
            $item['children'] = [];
            foreach ($node->getChildren() as $child) {
                $item['children'][] = $this->_getNodeJson($child, $level + 1);
            }
        }

        if (empty($item['children']) && (int) $node->getChildrenCount() > 0) {
            $item['children'] = [];
        }

        if (!empty($item['children'])) {
            $item['expanded'] = true;
        }

        if (in_array($node->getId(), $this->getCategoryIds())) {
            $item['checked'] = true;
        }

        return $item;
    }

    public function getRoot($parentNodeCategory = null, $recursionLevel = 3)
    {
        return $this->getRootByIds($this->getCategoryIds());
    }
}
