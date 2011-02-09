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
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shopping cart api for product
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Checkout_Model_Cart_Product_Api_V2 extends Mage_Checkout_Model_Cart_Product_Api
{

    protected function _prepareProductsData($data)
    {
        if (!is_array($data) && !is_object($data)) {
            return null;
        }

        $_data = array();
        if (is_object($data)) {
            $dataItem = $data;
            $dataItem = $this->_checkBundleOptions($dataItem);
            $dataItem = $this->_checkOptions($dataItem);
            $_data[] = get_object_vars($data);
        } else {
            foreach ($data as $_dataItem) {
                $dataItem = $_dataItem;
                $dataItem = $this->_checkBundleOptions($dataItem);
                $dataItem = $this->_checkOptions($dataItem);
                $_data[] = get_object_vars($dataItem);
            }
        }

        return parent::_prepareProductsData($_data);
    }

    protected function _checkBundleOptions($dataItem)
    {
        if (!isset($dataItem->bundle_options) || !isset($dataItem->bundle_options_qty)) {
            return $dataItem;
        }

        if (isset($dataItem->bundle_options)) {
            $options = array();
            foreach($dataItem->bundle_options as $option) {
                if (is_object($option)) {
                    $options[$option->key] = $option->value;
                } else {
                    foreach($option as $key=>$value) {
                        $options[$key] = $value;
                    }
                }
            }
            $dataItem->bundle_options = $options;
        }

        if (isset($dataItem->bundle_options_qty)) {
            $options_qty = array();
            foreach($dataItem->bundle_options_qty as $option) {
                if (is_object($option)) {
                    $options[$option->key] = $option->value;
                } else {
                    foreach($option as $key=>$value) {
                        $options[$key] = $value;
                    }
                }
            }
            $dataItem->bundle_options_qty = $options_qty;
        }
        return $dataItem;
    }

    protected function _checkOptions($dataItem)
    {
        if (isset($dataItem->options)) {
            $options = array();
            foreach($dataItem->options as $option) {
                if (is_object($option)) {
                    $options[$option->key] = $option->value;
                } else {
                    foreach($option as $key=>$value) {
                        $options[$key] = $value;
                    }
                }

            }
            $dataItem->options = $options;
        }
        return $dataItem;
    }
}
