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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml dashboard helper for products
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Helper_Dashboard_Product extends Mage_Adminhtml_Helper_Dashboard_Abstract
{

    protected function _initCollection()
    {
        $this->_collection = array(
            array('name'=>'Product 1', 'avarage'=>263, 'salary'=>3666),
            array('name'=>'Product 2', 'avarage'=>263, 'salary'=>266),
            array('name'=>'Product 3', 'avarage'=>263, 'salary'=>366),
            array('name'=>'Product 4', 'avarage'=>203, 'salary'=>6866),
            array('name'=>'Product 5', 'avarage'=>263, 'salary'=>566)
           );
    }

}
 // Class Mage_Adminhtml_Helper_Dashboard_Product end