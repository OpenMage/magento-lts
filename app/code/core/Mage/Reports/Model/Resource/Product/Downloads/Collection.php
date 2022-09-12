<?php
/**
 * OpenMage
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
 * @category   Mage
 * @package    Mage_Reports
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product Downloads Report collection
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Resource_Product_Downloads_Collection extends Mage_Catalog_Model_Resource_Product_Collection
{
    /**
     * Identifier field name
     *
     * @var string
     */
    protected $_idFieldName    = 'link_id';
    /**
     * Add downloads summary grouping by product
     *
     * @return $this
     */
    public function addSummary()
    {
        $adapter  = $this->getConnection();
        $linkExpr = $adapter->getIfNullSql('l_store.title', 'l.title');

        $this->getSelect()
            ->joinInner(
                ['d' =>  $this->getTable('downloadable/link_purchased_item')],
                'e.entity_id = d.product_id',
                [
                    'purchases' => new Zend_Db_Expr('SUM(d.number_of_downloads_bought)'),
                    'downloads' => new Zend_Db_Expr('SUM(d.number_of_downloads_used)'),
                ]
            )
            ->joinInner(
                ['l' => $this->getTable('downloadable/link_title')],
                'd.link_id = l.link_id',
                ['l.link_id']
            )
            ->joinLeft(
                ['l_store' => $this->getTable('downloadable/link_title')],
                $adapter->quoteInto('l.link_id = l_store.link_id AND l_store.store_id = ?', (int)$this->getStoreId()),
                ['link_title' => $linkExpr]
            )
            ->where(implode(' OR ', [
                $adapter->quoteInto('d.number_of_downloads_bought > ?', 0),
                $adapter->quoteInto('d.number_of_downloads_used > ?', 0),
            ]))
            ->group('d.link_id');
        /**
         * Allow to use analytic function
         */
        $this->_useAnalyticFunction = true;

        return $this;
    }

    /**
     * Add sorting
     *
     * @param string $attribute
     * @param string $dir
     * @return $this
     */
    public function setOrder($attribute, $dir = self::SORT_ORDER_DESC)
    {
        if ($attribute == 'purchases' || $attribute == 'downloads' || $attribute == 'link_title') {
            $this->getSelect()->order($attribute . ' ' . $dir);
        } else {
            parent::setOrder($attribute, $dir);
        }
        return $this;
    }

    /**
     * Add filtering
     *
     * @param string $field
     * @param string $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'link_title') {
            $conditionSql = $this->_getConditionSql('l.title', $condition);
            $this->getSelect()->where($conditionSql);
        } else {
            parent::addFieldToFilter($field, $condition);
        }
        return $this;
    }
}
