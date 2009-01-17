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
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Url rewrite resource collection model class
 *
 *
 * @category   Mage
 * @package    Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Mysql4_Url_Rewrite_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('core/url_rewrite');
    }

    /**
    * Add filter for tags (combined by OR)
    */
    public function addTagsFilter($tags)
    {
        $tagsArr = is_array($tags) ? $tags : explode(',', $tags);

        $sqlArr = array();
        foreach ($tagsArr as $t) {
            $sqlArr[] = $this->getConnection()->quoteInto("find_in_set(?, `tags`)", $t);
        }

        $cond = $this->getConnection()->quoteInto('`url_rewrite_id`=main_table.`url_rewrite_id` and `tag` in (?)', $tagsArr);
        $this->getSelect()->join($this->getTable('url_rewrite_tag'), $cond, array());
        return $this;
    }

    public function addStoreFilter($store)
    {
        $storeId = Mage::helper('core')->getStoreId($store);
        $this->getSelect()->where('store_id=0 or store_id=?', $storeId);
        return $this;
    }

    public function filterAllByProductId($productId)
    {
        $this->getSelect()
            ->where('id_path = ?', "product/{$productId}")
            ->orWhere('id_path like ?', "product/{$productId}/%");

        return $this;
    }

    public function filterAllByCategory()
    {
        $this->getSelect()
            ->where('id_path like ?', "category%");
        return $this;
    }
}