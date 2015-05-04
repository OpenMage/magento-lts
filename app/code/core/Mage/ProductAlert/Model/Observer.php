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
 * @package     Mage_ProductAlert
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * ProductAlert observer
 *
 * @category   Mage
 * @package    Mage_ProductAlert
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_ProductAlert_Model_Observer
{
    /**
     * Error email template configuration
     */
    const XML_PATH_ERROR_TEMPLATE   = 'catalog/productalert_cron/error_email_template';

    /**
     * Error email identity configuration
     */
    const XML_PATH_ERROR_IDENTITY   = 'catalog/productalert_cron/error_email_identity';

    /**
     * 'Send error emails to' configuration
     */
    const XML_PATH_ERROR_RECIPIENT  = 'catalog/productalert_cron/error_email';

    /**
     * Allow price alert
     *
     */
    const XML_PATH_PRICE_ALLOW      = 'catalog/productalert/allow_price';

    /**
     * Allow stock alert
     *
     */
    const XML_PATH_STOCK_ALLOW      = 'catalog/productalert/allow_stock';

    /**
     * Website collection array
     *
     * @var array
     */
    protected $_websites;

    /**
     * Warning (exception) errors array
     *
     * @var array
     */
    protected $_errors = array();

    /**
     * Retrieve website collection array
     *
     * @return array
     */
    protected function _getWebsites()
    {
        if (is_null($this->_websites)) {
            try {
                $this->_websites = Mage::app()->getWebsites();
            }
            catch (Exception $e) {
                $this->_errors[] = $e->getMessage();
            }
        }
        return $this->_websites;
    }

    /**
     * Process price emails
     *
     * @param Mage_ProductAlert_Model_Email $email
     * @return Mage_ProductAlert_Model_Observer
     */
    protected function _processPrice(Mage_ProductAlert_Model_Email $email)
    {
        $email->setType('price');
        foreach ($this->_getWebsites() as $website) {
            /* @var $website Mage_Core_Model_Website */

            if (!$website->getDefaultGroup() || !$website->getDefaultGroup()->getDefaultStore()) {
                continue;
            }
            if (!Mage::getStoreConfig(
                self::XML_PATH_PRICE_ALLOW,
                $website->getDefaultGroup()->getDefaultStore()->getId()
            )) {
                continue;
            }
            try {
                $collection = Mage::getModel('productalert/price')
                    ->getCollection()
                    ->addWebsiteFilter($website->getId())
                    ->setCustomerOrder();
            }
            catch (Exception $e) {
                $this->_errors[] = $e->getMessage();
                return $this;
            }

            $previousCustomer = null;
            $email->setWebsite($website);
            foreach ($collection as $alert) {
                try {
                    if (!$previousCustomer || $previousCustomer->getId() != $alert->getCustomerId()) {
                        $customer = Mage::getModel('customer/customer')->load($alert->getCustomerId());
                        if ($previousCustomer) {
                            $email->send();
                        }
                        if (!$customer) {
                            continue;
                        }
                        $previousCustomer = $customer;
                        $email->clean();
                        $email->setCustomer($customer);
                    }
                    else {
                        $customer = $previousCustomer;
                    }

                    $product = Mage::getModel('catalog/product')
                        ->setStoreId($website->getDefaultStore()->getId())
                        ->load($alert->getProductId());
                    if (!$product) {
                        continue;
                    }
                    $product->setCustomerGroupId($customer->getGroupId());
                    if ($alert->getPrice() > $product->getFinalPrice()) {
                        $productPrice = $product->getFinalPrice();
                        $product->setFinalPrice(Mage::helper('tax')->getPrice($product, $productPrice));
                        $product->setPrice(Mage::helper('tax')->getPrice($product, $product->getPrice()));
                        $email->addPriceProduct($product);

                        $alert->setPrice($productPrice);
                        $alert->setLastSendDate(Mage::getModel('core/date')->gmtDate());
                        $alert->setSendCount($alert->getSendCount() + 1);
                        $alert->setStatus(1);
                        $alert->save();
                    }
                }
                catch (Exception $e) {
                    $this->_errors[] = $e->getMessage();
                }
            }
            if ($previousCustomer) {
                try {
                    $email->send();
                }
                catch (Exception $e) {
                    $this->_errors[] = $e->getMessage();
                }
            }
        }
        return $this;
    }

    /**
     * Process stock emails
     *
     * @param Mage_ProductAlert_Model_Email $email
     * @return Mage_ProductAlert_Model_Observer
     */
    protected function _processStock(Mage_ProductAlert_Model_Email $email)
    {
        $email->setType('stock');

        foreach ($this->_getWebsites() as $website) {
            /* @var $website Mage_Core_Model_Website */

            if (!$website->getDefaultGroup() || !$website->getDefaultGroup()->getDefaultStore()) {
                continue;
            }
            if (!Mage::getStoreConfig(
                self::XML_PATH_STOCK_ALLOW,
                $website->getDefaultGroup()->getDefaultStore()->getId()
            )) {
                continue;
            }
            try {
                $collection = Mage::getModel('productalert/stock')
                    ->getCollection()
                    ->addWebsiteFilter($website->getId())
                    ->addStatusFilter(0)
                    ->setCustomerOrder();
            }
            catch (Exception $e) {
                $this->_errors[] = $e->getMessage();
                return $this;
            }

            $previousCustomer = null;
            $email->setWebsite($website);
            foreach ($collection as $alert) {
                try {
                    if (!$previousCustomer || $previousCustomer->getId() != $alert->getCustomerId()) {
                        $customer = Mage::getModel('customer/customer')->load($alert->getCustomerId());
                        if ($previousCustomer) {
                            $email->send();
                        }
                        if (!$customer) {
                            continue;
                        }
                        $previousCustomer = $customer;
                        $email->clean();
                        $email->setCustomer($customer);
                    }
                    else {
                        $customer = $previousCustomer;
                    }

                    $product = Mage::getModel('catalog/product')
                        ->setStoreId($website->getDefaultStore()->getId())
                        ->load($alert->getProductId());
                    /* @var $product Mage_Catalog_Model_Product */
                    if (!$product) {
                        continue;
                    }

                    $product->setCustomerGroupId($customer->getGroupId());

                    if ($product->isSalable()) {
                        $email->addStockProduct($product);

                        $alert->setSendDate(Mage::getModel('core/date')->gmtDate());
                        $alert->setSendCount($alert->getSendCount() + 1);
                        $alert->setStatus(1);
                        $alert->save();
                    }
                }
                catch (Exception $e) {
                    $this->_errors[] = $e->getMessage();
                }
            }

            if ($previousCustomer) {
                try {
                    $email->send();
                }
                catch (Exception $e) {
                    $this->_errors[] = $e->getMessage();
                }
            }
        }

        return $this;
    }

    /**
     * Send email to administrator if error
     *
     * @return Mage_ProductAlert_Model_Observer
     */
    protected function _sendErrorEmail()
    {
        if (count($this->_errors)) {
            if (!Mage::getStoreConfig(self::XML_PATH_ERROR_TEMPLATE)) {
                return $this;
            }

            $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);

            $emailTemplate = Mage::getModel('core/email_template');
            /* @var $emailTemplate Mage_Core_Model_Email_Template */
            $emailTemplate->setDesignConfig(array('area'  => 'backend'))
                ->sendTransactional(
                    Mage::getStoreConfig(self::XML_PATH_ERROR_TEMPLATE),
                    Mage::getStoreConfig(self::XML_PATH_ERROR_IDENTITY),
                    Mage::getStoreConfig(self::XML_PATH_ERROR_RECIPIENT),
                    null,
                    array('warnings' => join("\n", $this->_errors))
                );

            $translate->setTranslateInline(true);
            $this->_errors[] = array();
        }
        return $this;
    }

    /**
     * Run process send product alerts
     *
     * @return Mage_ProductAlert_Model_Observer
     */
    public function process()
    {
        $email = Mage::getModel('productalert/email');
        /* @var $email Mage_ProductAlert_Model_Email */
        $this->_processPrice($email);
        $this->_processStock($email);
        $this->_sendErrorEmail();

        return $this;
    }
}
