<?php
/**
 * OpenMage
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
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 * @deprecated after 1.4.1.0.
 */
class Mage_Core_Model_Design_Source_Apply extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $optionArray = [
                1 => Mage::helper('core')->__('This category and all its child elements'),
                3 => Mage::helper('core')->__('This category and its products only'),
                4 => Mage::helper('core')->__('This category and its child categories only'),
                2 => Mage::helper('core')->__('This category only')
            ];

            foreach ($optionArray as $k => $label) {
                $this->_options[] = ['value'=>$k, 'label'=>$label];
            }
        }

        return $this->_options;
    }
}
