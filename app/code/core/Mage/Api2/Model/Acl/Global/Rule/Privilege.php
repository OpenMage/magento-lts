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
 * @package     Mage_Api2
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Privilege of rule source model
 *
 * @category    Mage
 * @package     Mage_Api2
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Acl_Global_Rule_Privilege
{
    /**
     * Get options parameters
     *
     * @return array
     */
    static public function toOptionArray()
    {
        return array(
            array(
                'value' => Mage_Api2_Model_Resource::OPERATION_CREATE,
                'label' => Mage::helper('api2')->__('Create')
            ),
            array(
                'value' => Mage_Api2_Model_Resource::OPERATION_RETRIEVE,
                'label' => Mage::helper('api2')->__('Retrieve')
            ),
            array(
                'value' => Mage_Api2_Model_Resource::OPERATION_UPDATE,
                'label' => Mage::helper('api2')->__('Update')
            ),
            array(
                'value' => Mage_Api2_Model_Resource::OPERATION_DELETE,
                'label' => Mage::helper('api2')->__('Delete')
            )
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    static public function toArray()
    {
        return array(
            Mage_Api2_Model_Resource::OPERATION_CREATE   => Mage::helper('api2')->__('Create'),
            Mage_Api2_Model_Resource::OPERATION_RETRIEVE => Mage::helper('api2')->__('Retrieve'),
            Mage_Api2_Model_Resource::OPERATION_UPDATE   => Mage::helper('api2')->__('Update'),
            Mage_Api2_Model_Resource::OPERATION_DELETE   => Mage::helper('api2')->__('Delete')
        );
    }
}
