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
 * @category   Mage
 * @package    Mage_Reports
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Report Products Review collection
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Reports_Model_Mysql4_Review_Product_Collection extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
{
    public function joinReview()
    {
        $this->addAttributeToSelect('name');
        $this->getSelect()->join(
            array('r' => $this->getTable('review/review')),
            'e.entity_id=r.entity_pk_value',
            array(
                'review_cnt'    => 'COUNT(DISTINCT r.review_id)',
                'last_created'  => 'MAX(r.created_at)',
            )
        );

        $this->getSelect()->joinLeft(
            array('table_rating' => $this->getTable('rating_option_vote_aggregated')),
            'e.entity_id=table_rating.entity_pk_value AND table_rating.store_id>0',
            array(
                'avg_rating'          => 'SUM(table_rating.percent)/COUNT(table_rating.rating_id)',
                'avg_rating_approved' => 'SUM(table_rating.percent_approved)/COUNT(table_rating.rating_id)'
            )
        );
        $this->getSelect()->group('e.entity_id');

        return $this;
    }

    public function addAttributeToSort($attribute, $dir='asc')
    {
        if (in_array($attribute, array('review_cnt', 'last_created', 'avg_rating', 'avg_rating_approved'))) {
            $this->getSelect()->order($attribute.' '.$dir);
            return $this;
        }

        return parent::addAttributeToSort($attribute, $dir);
    }
}