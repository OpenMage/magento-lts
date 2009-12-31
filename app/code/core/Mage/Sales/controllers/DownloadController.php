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
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales controller for download purposes
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Sales_DownloadController extends Mage_Core_Controller_Front_Action
{

    /**
     * Custom options downloader
     */
    public function downloadCustomOptionAction ()
    {
        $quoteItemOptionId = $this->getRequest()->getParam('id');
        $secretKey = $this->getRequest()->getParam('key');
        $option = Mage::getModel('sales/quote_item_option')->load($quoteItemOptionId);

        if ($option->getId()) {

            try {
                $info = unserialize($option->getValue());

                if ($secretKey != $info['secret_key']) {
                    throw new Exception();
                }

                $filePath = Mage::getBaseDir() . $info['order_path'];
                if (!is_file($filePath) || !is_readable($filePath)) {
                    // try get file from quote
                    $filePath = Mage::getBaseDir() . $info['quote_path'];
                    if (!is_file($filePath) || !is_readable($filePath)) {
                        throw new Exception();
                    }
                }

                $this->getResponse()
                    ->setHttpResponseCode(200)
                    ->setHeader('Pragma', 'public', true)
                    ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
                    ->setHeader('Content-type', $info['type'], true)
                    ->setHeader('Content-Length', $info['size'])
                    ->setHeader('Content-Disposition', 'inline' . '; filename='.$info['title']);

                $this->getResponse()
                    ->clearBody();
                $this->getResponse()
                    ->sendHeaders();

                readfile($filePath);

            } catch (Exception $e) {
                $this->_forward('noRoute');
            }

        } else {
            $this->_forward('noRoute');
        }
    }
}
