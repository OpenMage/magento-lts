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
 * @package     Mage_Reports
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Reports summary collection
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Mysql4_Entity_Summary_Collection_Abstract extends Varien_Data_Collection
{
    /**
     * Entity collection for summaries
     *
     * @var Mage_Entity_Model_Entity_Collection_Abstract
     */
    protected $_entityCollection;

    /**
     * Loads and calculates summaries
     *
     * @param boolean $printQuery
     * @param boolean $logQuery
     * @return Varien_Data_Collection
     */
    /*public function load($printQuery=false, $logQuery=false)
    {
        return $this;
    }*/

    /**
     * Filters the summaries by some period
     *
     * @param string $periodType
     * @param string|int|null $customStart
     * @param string|int|null $customEnd
     * @return Varien_Data_Collection
     */
    public function setSelectPeriod($periodType, $customStart=null, $customEnd=null)
    {
        switch ($periodType) {
            case "24h":
                $customStart = time()-24*60*60;
                $customEnd   = time();
                break;

            case "7d":
                $customStart = time()-7*24*60*60;
                $customEnd   = time();
                break;

            case "30d":
                $customStart = time()-30*24*60*60;
                $customEnd   = time();
                break;

            case "1y":
                $customStart = time()-365*24*60*60;
                $customEnd   = time();
                break;

            default:
                if(is_string($customStart)) {
                    $customStart = strtotime($customStart);
                }
                if(is_string($customEnd)) {
                    $customEnd = strtotime($customEnd);
                }
                break;

        }


        return $this;
    }


    public function setDatePeriod($period)
    {

    }

    public function setStoreFilter($storeId)
    {
        return $this;
    }

    /**
     * Return collection for summaries
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getCollection()
    {
        if (empty($this->_entityCollection)) {
            $this->_initCollection();
        }
        return $this->_entityCollection;
    }

    protected function _initCollection()
    {
        return $this;
    }

}
