<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Config category field backend
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Category extends Mage_Core_Model_Config_Data
{
    protected function _afterSave()
    {
        if ($this->getScope() == 'stores') {
            $rootId     = $this->getValue();
            $storeId    = $this->getScopeId();

            $category   = Mage::getSingleton('catalog/category');
            $tree       = $category->getTreeModel();

            // Create copy of categories attributes for chosen store
            $tree->load();
            $root = $tree->getNodeById($rootId);

            // Save root
            $category->setStoreId(0)
               ->load($root->getId());
            $category->setStoreId($storeId)
                ->save();

            foreach ($root->getAllChildNodes() as $node) {
                $category->setStoreId(0)
                   ->load($node->getId());
                $category->setStoreId($storeId)
                    ->save();
            }
        }
        return $this;
    }
}
