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
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Category resource collection
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Mysql4_Category_Collection
    extends Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection
{
    /**
     * Level of parent categories
     */
    const PARENT_CATEGORIES_LEVEL = 2;

    /**
     * Before collection load
     *
     * @return Mage_XmlConnect_Model_Mysql4_Category_Collection
     */
    protected function _beforeLoad()
    {
        $this->addNameToResult();
        $this->addAttributeToSelect('thumbnail');
        $this->addIsActiveFilter();
        return parent::_beforeLoad();
    }

    /**
     * Adding filter level
     *
     * @param string $level
     * @return Mage_XmlConnect_Model_Mysql4_Category_Collection
     */
    public function addLevelExactFilter($level)
    {
        $this->getSelect()->where('e.level = ?', $level);
        return $this;
    }

    /**
     * Set limit collection
     * 
     * @param int $offset
     * @param int $count
     * @return Mage_XmlConnect_Model_Mysql4_Category_Collection
     */
    public function setLimit($offset, $count)
    {
        $this->getSelect()->limit($count, $offset);
        return $this;
    }

    /**
     * Adding parentCategory filter
     *
     * @param int $parentId
     * @return Mage_XmlConnect_Model_Mysql4_Category_Collection
     */
    public function addParentIdFilter($parentId)
    {
        if (!is_null($parentId)) {
            $this->getSelect()->where('e.parent_id = ?', (int)$parentId);
        } else {
            $this->addLevelExactFilter(self::PARENT_CATEGORIES_LEVEL);
        }
        return $this;
    }
}
