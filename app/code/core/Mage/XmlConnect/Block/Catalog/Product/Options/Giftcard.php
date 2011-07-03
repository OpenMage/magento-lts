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
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Gift Card product options xml renderer
 *
 * @category   Mage
 * @package    Mage_XmlConnect
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Catalog_Product_Options_Giftcard extends Mage_XmlConnect_Block_Catalog_Product_Options
{
    /**
     * Get sender name
     *
     * @return string
     */
    public function getSenderName()
    {
        $senderName = $this->getDefaultValue('giftcard_sender_name');
        if (!strlen($senderName)) {
            $firstName = (string) Mage::getSingleton('customer/session')->getCustomer()->getFirstname();
            $lastName  = (string) Mage::getSingleton('customer/session')->getCustomer()->getLastname();

            if ($firstName && $lastName) {
                $senderName = $firstName . ' ' . $lastName;
            } else {
                $senderName = '';
            }
        }
        return $senderName;
    }

    /**
     * Get sender email
     *
     * @return string
     */
    public function getSenderEmail()
    {
        $senderEmail = $this->getDefaultValue('giftcard_sender_email');

        if (!strlen($senderEmail)) {
            $senderEmail = (string) Mage::getSingleton('customer/session')->getCustomer()->getEmail();
        }
        return $senderEmail;
    }

    /**
     * Get preconfigured values from product
     *
     * @param  $value param id
     * @return string
     */
    protected function getDefaultValue($value)
    {
        if ($this->getProduct()) {
            return (string) $this->getProduct()->getPreconfiguredValues()->getData($value);
        } else {
            return '';
        }
    }

    /**
     * Check is message available for current product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool|int
     */
    public function isMessageAvailable(Mage_Catalog_Model_Product $product)
    {
        if ($product->getUseConfigAllowMessage()) {
            return Mage::getStoreConfigFlag(Enterprise_GiftCard_Model_Giftcard::XML_PATH_ALLOW_MESSAGE);
        } else {
            return (int) $product->getAllowMessage();
        }
    }

    /**
     * Check is email available for product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function isEmailAvailable(Mage_Catalog_Model_Product $product)
    {
        if ($product->getTypeInstance()->isTypePhysical()) {
            return false;
        }
        return true;
    }

    /**
     * Is amount available for product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function isAmountAvailable(Mage_Catalog_Model_Product $product)
    {
        if (!$product->getGiftcardAmounts()) {
            return false;
        }
        return true;
    }

    /**
     * Generate gift card product options xml
     *
     * @param Mage_Catalog_Model_Product $product
     * @param bool $isObject
     * @return string | Mage_XmlConnect_Model_Simplexml_Element
     */
    public function getProductOptionsXml(Mage_Catalog_Model_Product $product, $isObject = false)
    {
        /** set current product object */
        $this->setProduct($product);

        /** @var $xmlModel Mage_XmlConnect_Model_Simplexml_Element */
        $xmlModel = $this->getProductCustomOptionsXmlObject($product);

        /** @var $optionsXmlObj Mage_XmlConnect_Model_Simplexml_Element */
        $optionsXmlObj = $xmlModel->options;

        if (!$product->isSaleable()) {
            return $isObject ? $xmlModel : $xmlModel->asNiceXml();
        }

        /** @var $test Enterprise_GiftCard_Model_Catalog_Product_Type_Giftcard */
        $giftCard = $product->getTypeInstance(true);

        /** @var $priceModel Enterprise_GiftCard_Block_Catalog_Product_Price */
        $priceModel = $product->getPriceModel();

        /** @var $coreHelper Mage_Core_Helper_Data */
        $coreHelper = Mage::helper('core');

        if ($this->isAmountAvailable($product)) {

            $configValue = $this->getDefaultValue('giftcard_amount');

            /**
             * Render fixed amounts options
             */
            if (count($amounts = $priceModel->getSortedAmounts($product))) {
                $amountNode = $optionsXmlObj->addChild('fixed_amounts');
                foreach ($amounts as $price) {
                    $amount = $amountNode->addChild('amount');
                    if ($configValue == $price) {
                        $amount->addAttribute('selected', 1);
                    }
                    $amount->addAttribute(
                        'price',
                        $coreHelper->currency($price, true, false)
                    );
                }
            }

            /**
             * Render open amount options
             */
            $openAmountNode = $optionsXmlObj->addChild('open_amount');
            if ($product->getAllowOpenAmount()) {
                $openAmountNode->addAttribute('enabled', 1);
                if ($configValue == 'custom') {
                    $openAmountNode->addAttribute(
                        'selected_amount',
                        $this->getDefaultValue('custom_giftcard_amount')
                    );
                }
                if ($priceModel->getMinAmount($product)) {
                    $minAmount = $xmlModel->xmlentities(
                        $coreHelper->currency(
                            $product->getOpenAmountMin(),
                            true,
                            false
                        )
                    );
                } else {
                    $minAmount = 0;
                }
                $openAmountNode->addAttribute('min_amount', $minAmount);

                if ($priceModel->getMaxAmount($product)) {
                    $maxAmount = $xmlModel->xmlentities(
                        $coreHelper->currency(
                            $product->getOpenAmountMax(),
                            true,
                            false
                        )
                    );
                } else {
                    $maxAmount = 0;
                }
                $openAmountNode->addAttribute('max_amount', $maxAmount);
            } else {
                $openAmountNode->addAttribute('enabled', 0);
            }
        }

        /**
         * Render Gift Card form options
         */
        $form = $optionsXmlObj->addCustomChild('form', null, array(
                'name'      => 'giftcard-send-form',
                'method'    => 'post'
            )
        );

        $senderFieldset = $form->addCustomChild('fieldset', null, array(
                'legend' => $this->__('Sender Information')
            )
        );

        $senderFieldset->addField('giftcard_sender_name', 'text', array(
                'label'     => Mage::helper('enterprise_giftcard')->__('Sender Name'),
                'required'  => 'true',
                'value'     => $this->getSenderName()
            )
        );

        $recipientFieldset = $form->addCustomChild('fieldset', null, array(
                'legend' => $this->__('Recipient Information')
            )
        );

        $recipientFieldset->addField('giftcard_recipient_name', 'text', array(
                'label'     => Mage::helper('enterprise_giftcard')->__('Recipient Name'),
                'required'  => 'true',
                'value'     => $this->getDefaultValue('giftcard_recipient_name')
            )
        );

        if ($this->isEmailAvailable($product)) {
            $senderFieldset->addField('giftcard_sender_email', 'text', array(
                    'label'     => Mage::helper('enterprise_giftcard')->__('Sender Email'),
                    'required'  => 'true',
                    'value'     => $this->getSenderEmail()
                )
            );

            $recipientFieldset->addField('giftcard_recipient_email', 'text', array(
                    'label'     => Mage::helper('enterprise_giftcard')->__('Recipient Email'),
                    'required'  => 'true',
                    'value'     => $this->getDefaultValue('giftcard_recipient_email')
                )
            );
        }

        if ($this->isMessageAvailable($product)) {
            $messageMaxLength = (int) Mage::getStoreConfig(
                Enterprise_GiftCard_Model_Giftcard::XML_PATH_MESSAGE_MAX_LENGTH
            );
            $recipientFieldset->addField('giftcard_message', 'text', array(
                    'label'     => Mage::helper('enterprise_giftcard')->__('Message'),
                    'required'  => 'false',
                    'max_length'=> $messageMaxLength,
                    'value'     => $this->getDefaultValue('giftcard_message')
                )
            );
        }

        return $isObject ? $xmlModel : $xmlModel->asNiceXml();
    }
}
