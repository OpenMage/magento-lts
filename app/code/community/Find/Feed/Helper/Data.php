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
 * @category    Find
 * @package     Find_Feed
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * TheFind feed helper
 *
 * @category   Find
 * @package    Find_Feed
 */
class Find_Feed_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Checking if some required attributes missed
     *
     * @param array $attributes
     * @return bool
     */
    public function checkRequired($attributes) 
    {
        $attributeConfig = Mage::getConfig()->getNode(Find_Feed_Model_Import::XML_NODE_FIND_FEED_ATTRIBUTES);
        $attributeRequired = array();
        foreach ($attributeConfig->children() as $ac) {
            if ((int)$ac->required) {
                $attributeRequired[] = (string)$ac->label;
            }
        }

        foreach ($attributeRequired as $value) {
            if (!isset($attributes[$value])) { 
                return false;
            }
        }
        return true;
    }

    /**
     * Product entity type
     *
     * @return int
     */
    public function getProductEntityType()
    {
        return Mage::getSingleton('eav/config')->getEntityType('catalog_product')->getId();
    }
}
