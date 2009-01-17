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
 * @category   Mage
 * @package    Mage_Tag
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tag data helper
 */
class Mage_Tag_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getStatusesArray()
    {
        return array(
            Mage_Tag_Model_Tag::STATUS_DISABLED => Mage::helper('tag')->__('Disabled'),
            Mage_Tag_Model_Tag::STATUS_PENDING  => Mage::helper('tag')->__('Pending'),
            Mage_Tag_Model_Tag::STATUS_APPROVED => Mage::helper('tag')->__('Approved')
        );
    }

    public function getStatusesOptionsArray()
    {
        return array(
            array(
                'label' => Mage::helper('tag')->__('Disabled'),
                'value' => Mage_Tag_Model_Tag::STATUS_DISABLED
            ),
            array(
                'label' => Mage::helper('tag')->__('Pending'),
                'value' => Mage_Tag_Model_Tag::STATUS_PENDING
            ),
            array(
                'label' => Mage::helper('tag')->__('Approved'),
                'value' => Mage_Tag_Model_Tag::STATUS_APPROVED
            )
        );
    }
}
