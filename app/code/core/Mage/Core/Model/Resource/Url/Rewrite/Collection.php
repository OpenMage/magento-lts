<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */

/**
 * Url rewrite resource collection model class
 *
 * @category   Mage
 * @package    Mage_Core
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
     * @return $this
     */
    public function addTagsFilter($tags)
    {
        $tags = is_array($tags) ? $tags : explode(',', $tags);

        if (!$this->getFlag('tag_table_joined')) {
            $this->join(
                ['curt' => $this->getTable('core/url_rewrite_tag')],
                'main_table.url_rewrite_id = curt.url_rewrite_id',
                [],
            );
            $this->setFlag('tag_table_joined', true);
        }

        $this->addFieldToFilter('curt.tag', ['in' => $tags]);
        return $this;
    }

    /**
     * Filter collections by stores
     *
     * @param mixed $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!is_array($store)) {
            $store = [Mage::app()->getStore($store)->getId()];
        }
        if ($withAdmin) {
            $store[] = 0;
        }

        $this->addFieldToFilter('store_id', ['in' => $store]);

        return $this;
    }

    /**
     *  Add filter by catalog product Id
     *
     * @param int $productId
     * @return $this
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
     * @return $this
     */
    public function filterAllByCategory()
    {
        $this->getSelect()
            ->where('id_path LIKE ?', 'category/%');
        return $this;
    }
}
