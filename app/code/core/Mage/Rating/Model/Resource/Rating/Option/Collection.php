<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Rating
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Rating option collection
 *
 * @category   Mage
 * @package    Mage_Rating
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rating_Model_Resource_Rating_Option_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Rating options table
     *
     * @var string
     * @deprecated since 1.5.0.0
     */
    protected $_ratingOptionTable;

    /**
     * Rating votes table
     *
     * @var string
     */
    protected $_ratingVoteTable;

    /**
     * Define model
     *
     */
    protected function _construct()
    {
        $this->_init('rating/rating_option');
        $this->_ratingOptionTable   = $this->getTable('rating/rating_option');
        $this->_ratingVoteTable     = $this->getTable('rating/rating_option_vote');
    }

    /**
     * Add rating filter
     *
     * @param   int|array $rating
     * @return  $this
     */
    public function addRatingFilter($rating)
    {
        if (is_numeric($rating)) {
            $this->addFilter('rating_id', $rating);
        } elseif (is_array($rating)) {
            $this->addFilter('rating_id', $this->_getConditionSql('rating_id', ['in' => $rating]), 'string');
        }
        return $this;
    }

    /**
     * Set order by position field
     *
     * @param   string $dir
     * @return  $this
     */
    public function setPositionOrder($dir = 'ASC')
    {
        $this->setOrder('main_table.position', $dir);
        return $this;
    }
}
