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
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product MAP "Display Actual Price" attribute source
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Attribute_Source_Msrp_Type
    extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Display Product Price on gesture
     */
    const TYPE_ON_GESTURE = '1';

    /**
     * Display Product Price in cart
     */
    const TYPE_IN_CART    = '2';

    /**
     * Display Product Price before order confirmation
     */
    const TYPE_BEFORE_ORDER_CONFIRM = '3';

    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = array(
                array(
                    'label' => Mage::helper('catalog')->__('In Cart'),
                    'value' => self::TYPE_IN_CART
                ),
                array(
                    'label' => Mage::helper('catalog')->__('Before Order Confirmation'),
                    'value' => self::TYPE_BEFORE_ORDER_CONFIRM
                ),
                array(
                    'label' => Mage::helper('catalog')->__('On Gesture'),
                    'value' => self::TYPE_ON_GESTURE
                ),
            );
        }
        return $this->_options;
    }

    /**
     * Get options as array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}
