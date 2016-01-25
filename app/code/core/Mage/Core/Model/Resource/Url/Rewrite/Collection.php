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
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Url rewrite resource collection model class
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Resource_Url_Rewrite_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Define resource model
     *
     */
    protected function _construct()
    {
        $this->_init('core/url_rewrite');
    }

    /**
     * Add filter for tags (combined by OR)
     *
     * @param string|array $tags
     * @return Mage_Core_Model_Resource_Url_Rewrite_Collection
     */
    public function addTagsFilter($tags)
    {
        $tags = is_array($tags) ? $tags : explode(',', $tags);

        if (!$this->getFlag('tag_table_joined')) {
            $this->join(
                array('curt' => $this->getTable('core/url_rewrite_tag')),
                'main_table.url_rewrite_id = curt.url_rewrite_id',
                array());
            $this->setFlag('tag_table_joined', true);
        }

        $this->addFieldToFilter('curt.tag', array('in' => $tags));
        return $this;
    }

    /**
     * Filter collections by stores
     *
     * @param mixed $store
     * @param bool $withAdmin
     * @return Mage_Core_Model_Resource_Url_Rewrite_Collection
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!is_array($store)) {
            $store = array(Mage::app()->getStore($store)->getId());
        }
        if ($withAdmin) {
            $store[] = 0;
        }

        $this->addFieldToFilter('store_id', array('in' => $store));

        return $this;
    }

    /**
     *  Add filter by catalog product Id
     *
     * @param int $productId
     * @return Mage_Core_Model_Resource_Url_Rewrite_Collection
     */
    public function filterAllByProductId($productId)
    {
        $this->getSelect()
            ->where('id_path = ?', "product/{$productId}")
            ->orWhere('id_path LIKE ?', "product/{$productId}/%");

        return $this;
    }

    /**
     * Add filter by all catalog category
     *
     * @return Mage_Core_Model_Resource_Url_Rewrite_Collection
     */
    public function filterAllByCategory()
    {
        $this->getSelect()
            ->where('id_path LIKE ?', "category/%");
        return $this;
    }
}
