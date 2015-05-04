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
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect Images resource collection
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Xmlconnect_Model_Resource_Images_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Init resource model
     */
    protected function _construct()
    {
        $this->_init('xmlconnect/images');
    }

    /**
     * Add application filter
     *
     * @param integer $appId
     * @return Mage_Xmlconnect_Model_Images_Collection
     */
    public function addApplicationToFilter($appId)
    {
        $this->addFieldToFilter('application_id', $appId);
        return $this;
    }

    /**
     * Add image type filter
     *
     * @param integer $type
     * @return Mage_Xmlconnect_Model_Images_Collection
     */
    public function addImageTypeToFilter($type)
    {
        $this->addFieldToFilter('image_type', $type);
        return $this;
    }

    /**
     * Set Order by position
     *
     * @return Mage_Xmlconnect_Model_Images_Collection
     */
    public function setPositionOrder()
    {
        $this->setOrder('main_table.order', self::SORT_ORDER_ASC);
        return $this;
    }

    /**
     * Sets a limit count and offset to the query.
     *
     * @param int $count OPTIONAL The number of rows to return.
     * @param int $offset OPTIONAL Start returning after this many rows.
     * @return Mage_Xmlconnect_Model_Resource_Images_Collection
     */
    public function setLimit($count = null, $offset = null)
    {
        $this->getSelect()->limit($count, $offset);
        return $this;
    }
}
