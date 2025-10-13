<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ProductAlert
 */

/**
 * ProductAlert observer
 *
 * @package    Mage_ProductAlert
 */
class Mage_ProductAlert_Model_Observer
{
    /**
     * Error email template configuration
     */
    public const XML_PATH_ERROR_TEMPLATE   = 'catalog/productalert_cron/error_email_template';

    /**
     * Error email identity configuration
     */
    public const XML_PATH_ERROR_IDENTITY   = 'catalog/productalert_cron/error_email_identity';

    /**
     * 'Send error emails to' configuration
     */
    public const XML_PATH_ERROR_RECIPIENT  = 'catalog/productalert_cron/error_email';

    /**
     * Allow price alert
     *
     */
    public const XML_PATH_PRICE_ALLOW      = 'catalog/productalert/allow_price';

    /**
     * Allow stock alert
     *
     */
    public const XML_PATH_STOCK_ALLOW      = 'catalog/productalert/allow_stock';

    /**
     * Website collection array
     *
     * @var array|null
     */
    protected $_websites;

    /**
     * Warning (exception) errors array
     *
     * @var array
     */
    protected $_errors = [];

    /**
     * Retrieve website collection array
     *
     * @return Mage_Core_Model_Website[]
     */
    protected function _getWebsites()
    {
        if (is_null($this->_websites)) {
            try {
                $this->_websites = Mage::app()->getWebsites();
            } catch (Exception $e) {
                $this->_errors[] = $e->getMessage();
            }
        }

        return $this->_websites;
    }

    /**
     * Process price emails
     *
     * @return $this
     */
    protected function _processPrice(Mage_ProductAlert_Model_Email $email)
    {
        $email->setType('price');
        $originalStore = Mage::app()->getStore();
        foreach ($this->_getWebsites() as $website) {
            /** @var Mage_Core_Model_Website $website */

            if (!$website->getDefaultGroup() || !$website->getDefaultGroup()->getDefaultStore()) {
                continue;
            }

            if (!Mage::getStoreConfig(self::XML_PATH_PRICE_ALLOW, $website->getDefaultGroup()->getDefaultStore()->getId())) {
                continue;
            }

            try {
                $collection = Mage::getModel('productalert/price')
                    ->getCollection()
                    ->addWebsiteFilter($website->getId())
                    ->setCustomerOrder();
            } catch (Exception $e) {
                $this->_errors[] = $e->getMessage();
                return $this;
            }

            $previousCustomer = null;
            $email->setWebsite($website);
            Mage::app()->setCurrentStore($website->getDefaultGroup()->getDefaultStore());
            /** @var Mage_ProductAlert_Model_Price $alert */
            foreach ($collection as $alert) {
                try {
                    if (!$previousCustomer || $previousCustomer->getId() != $alert->getCustomerId()) {
                        $customer = Mage::getModel('customer/customer')->load($alert->getCustomerId());
                        if ($previousCustomer) {
                            $email->send();
                        }

                        if (!$customer->getId()) {
                            continue;
                        }

                        $previousCustomer = $customer;
                        $email->clean();
                        $email->setCustomer($customer);
                    } else {
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
                } catch (Exception $e) {
                    $this->_errors[] = $e->getMessage();
                }
            }

            if ($previousCustomer) {
                try {
                    $email->send();
                } catch (Exception $e) {
                    $this->_errors[] = $e->getMessage();
                }
            }
        }

        Mage::app()->setCurrentStore($originalStore);
        return $this;
    }

    /**
     * Process stock emails
     *
     * @return $this
     */
    protected function _processStock(Mage_ProductAlert_Model_Email $email)
    {
        $email->setType('stock');
        $originalStore = Mage::app()->getStore();

        foreach ($this->_getWebsites() as $website) {
            /** @var Mage_Core_Model_Website $website */

            if (!$website->getDefaultGroup() || !$website->getDefaultGroup()->getDefaultStore()) {
                continue;
            }

            if (!Mage::getStoreConfig(self::XML_PATH_STOCK_ALLOW, $website->getDefaultGroup()->getDefaultStore()->getId())) {
                continue;
            }

            try {
                $collection = Mage::getModel('productalert/stock')
                    ->getCollection()
                    ->addWebsiteFilter($website->getId())
                    ->addStatusFilter(0)
                    ->setCustomerOrder();
            } catch (Exception $e) {
                $this->_errors[] = $e->getMessage();
                return $this;
            }

            $previousCustomer = null;
            $email->setWebsite($website);
            Mage::app()->setCurrentStore($website->getDefaultGroup()->getDefaultStore());
            /** @var Mage_ProductAlert_Model_Stock $alert */
            foreach ($collection as $alert) {
                try {
                    if (!$previousCustomer || $previousCustomer->getId() != $alert->getCustomerId()) {
                        $customer = Mage::getModel('customer/customer')->load($alert->getCustomerId());
                        if ($previousCustomer) {
                            $email->send();
                        }

                        if (!$customer->getId()) {
                            continue;
                        }

                        $previousCustomer = $customer;
                        $email->clean();
                        $email->setCustomer($customer);
                    } else {
                        $customer = $previousCustomer;
                    }

                    $product = Mage::getModel('catalog/product')
                        ->setStoreId($website->getDefaultStore()->getId())
                        ->load($alert->getProductId());

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
                } catch (Exception $e) {
                    $this->_errors[] = $e->getMessage();
                }
            }

            if ($previousCustomer) {
                try {
                    $email->send();
                } catch (Exception $e) {
                    $this->_errors[] = $e->getMessage();
                }
            }
        }

        Mage::app()->setCurrentStore($originalStore);

        return $this;
    }

    /**
     * Send email to administrator if error
     *
     * @return $this
     */
    protected function _sendErrorEmail()
    {
        if (count($this->_errors)) {
            if (!Mage::getStoreConfig(self::XML_PATH_ERROR_TEMPLATE)) {
                return $this;
            }

            $translate = Mage::getSingleton('core/translate');
            /** @var Mage_Core_Model_Translate $translate */
            $translate->setTranslateInline(false);

            $emailTemplate = Mage::getModel('core/email_template');
            /** @var Mage_Core_Model_Email_Template $emailTemplate */
            $emailTemplate->setDesignConfig(['area'  => 'backend'])
                ->sendTransactional(
                    Mage::getStoreConfig(self::XML_PATH_ERROR_TEMPLATE),
                    Mage::getStoreConfig(self::XML_PATH_ERROR_IDENTITY),
                    Mage::getStoreConfig(self::XML_PATH_ERROR_RECIPIENT),
                    null,
                    ['warnings' => implode("\n", $this->_errors)],
                );

            $translate->setTranslateInline(true);
            $this->_errors[] = [];
        }

        return $this;
    }

    /**
     * Run process send product alerts
     *
     * @return $this
     */
    public function process()
    {
        $email = Mage::getModel('productalert/email');
        $this->_processPrice($email);
        $this->_processStock($email);
        $this->_sendErrorEmail();

        return $this;
    }
}
