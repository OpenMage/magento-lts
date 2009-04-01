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
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Catalog_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_redirect('/');
    }

    public function productAction()
    {
        $productIds = array(
            16,17,18,19,20,25,26,27,28,29,30,31,32,33,34,35,36,37
,38,39,41,42,44,45,46,47,48,49,51,52,53,74,75,79,80,81,82,84,85,86,87,88,89,90,91,92,94,95,96,97,99,100
,101,102,104,105,106,107,109,110,111,113,114,115,117,118,121,122,124,125,127,128,129,130,131,132,133
,134,137,138,139,140,141,143,145,147,148,149,150,151,152,153,154,155,156,157,159,160,161,162,166
        );
//        $productIds = array(
//            26
//        );
        $time = microtime(true);
        for ($i = 0; $i < 5000; $i ++)
        {
            $productId = array_rand($productIds, 1);
            $productId = $productIds[$productId];
            $product = Mage::getModel('catalog/product');
            $memory = memory_get_usage();
            $product
                ->load($productId);

            // kill options
            foreach($product->getOptions() as $option) {
                /* @var $option Mage_Catalog_Model_Product_Option */
                foreach ($option->getValues() as $value) {
                    /* @var $value Mage_Catalog_Model_Product_Option_Value */
                    $value->setProduct(null);
                    $value->unsetOption();
                }
                $option->setProduct(null);
            }
            $product->setOptions(array());
            //$product->setCustomOptions(array());
//            $product->getStockItem()
//                ->setProduct(null);
//            $product->setStockItem(null);
            /* @var $product Mage_Catalog_Model_Product */
            echo sprintf("%04d. sku: %s, time: %.3f, memory: %.3f/%.3f Mb<br />",
                $i,
                $product->getSku(),
                microtime(true) - $time,
                $memory / 1024 / 1024,
                memory_get_usage() / 1024 / 1024
            );

//            var_dump($product);
//
//
//
//            var_dump( $product->debug() );
//            die();
            $time = microtime(true);
        }
    }
}