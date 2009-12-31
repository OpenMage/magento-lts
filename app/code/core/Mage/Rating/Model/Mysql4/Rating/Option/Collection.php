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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Rating
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Rating option collection
 *
 * @category   Mage
 * @package    Mage_Rating
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rating_Model_Mysql4_Rating_Option_Collection extends Varien_Data_Collection_Db
{
    protected $_ratingOptionTable;
    protected $_ratingVoteTable;

    public function __construct()
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('rating_read'));
        $this->_ratingOptionTable   = Mage::getSingleton('core/resource')->getTableName('rating/rating_option');
        $this->_ratingVoteTable     = Mage::getSingleton('core/resource')->getTableName('rating/rating_vote');

        $this->_select->from($this->_ratingOptionTable);

        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('rating/rating_option'));
    }

    /**
     * add rating filter
     *
     * @param   int|array $rating
     * @return  Varien_Data_Collection_Db
     */
    public function addRatingFilter($rating)
    {
        if (is_numeric($rating)) {
            $this->addFilter('rating_id', $rating);
        }
        elseif (is_array($rating)) {
            $this->addFilter('rating_id', $this->_getConditionSql('rating_id', array('in'=>$rating)), 'string');
        }
        return $this;
    }

    /**
     * set order by position field
     *
     * @param   string $dir
     * @return  Varien_Data_Collection_Db
     */
    public function setPositionOrder($dir='ASC')
    {
        $this->setOrder($this->_ratingOptionTable.'.position', $dir);
        return $this;
    }
}
