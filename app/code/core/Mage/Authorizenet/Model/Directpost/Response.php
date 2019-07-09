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
 * @package     Mage_Authorizenet
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Authorize.net response model for DirectPost model.
 *
 * @category   Mage
 * @package    Mage_Authorizenet
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Authorizenet_Model_Directpost_Response extends Varien_Object
{
    /**
     * Generates an Md5 hash to compare against AuthNet's.
     *
     * @param string $merchantMd5
     * @param string $merchantApiLogin
     * @param string $amount
     * @param string $transactionId
     * @return string
     */
    public function generateHash($merchantMd5, $merchantApiLogin, $amount, $transactionId)
    {
        return strtoupper(md5($merchantMd5 . $merchantApiLogin . $transactionId . $amount));
    }

    /**
     * Return if is valid order id.
     *
     * @param string $storedHash
     * @param string $merchantApiLogin
     * @return bool
     */
    public function isValidHash($storedHash, $merchantApiLogin)
    {
        if (empty($this->getData('x_amount'))) {
            $this->setData('x_amount', '0.00');
        }

        if (!empty($this->getData('x_SHA2_Hash'))) {
            $hash = $this->generateSha2Hash($storedHash);
            return $hash == $this->getData('x_SHA2_Hash');
        } elseif (!empty($this->getData('x_MD5_Hash'))) {
            $hash = $this->generateHash($storedHash, $merchantApiLogin, $this->getXAmount(), $this->getXTransId());
            return $hash == $this->getData('x_MD5_Hash');
        }

        return false;
    }

    /**
     * Return if this is approved response from Authorize.net auth request.
     *
     * @return bool
     */
    public function isApproved()
    {
        return $this->getXResponseCode() == Mage_Authorizenet_Model_Directpost::RESPONSE_CODE_APPROVED;
    }

    /**
     * Generates an SHA2 hash to compare against AuthNet's.
     *
     * @param string $signatureKey
     * @return string
     * @see https://support.authorize.net/s/article/MD5-Hash-End-of-Life-Signature-Key-Replacement
     */
    public function generateSha2Hash($signatureKey)
    {
        $hashFields = [
            'x_trans_id',
            'x_test_request',
            'x_response_code',
            'x_auth_code',
            'x_cvv2_resp_code',
            'x_cavv_response',
            'x_avs_code',
            'x_method',
            'x_account_number',
            'x_amount',
            'x_company',
            'x_first_name',
            'x_last_name',
            'x_address',
            'x_city',
            'x_state',
            'x_zip',
            'x_country',
            'x_phone',
            'x_fax',
            'x_email',
            'x_ship_to_company',
            'x_ship_to_first_name',
            'x_ship_to_last_name',
            'x_ship_to_address',
            'x_ship_to_city',
            'x_ship_to_state',
            'x_ship_to_zip',
            'x_ship_to_country',
            'x_invoice_num',
        ];

        $order = Mage::getModel('sales/order')->loadByIncrementId($this->getData('x_invoice_num'));
        $billing = $order->getBillingAddress();
        if (!empty($billing)) {
            $this->setXFirstName(strval($billing->getFirstname()))
                ->setXLastName(strval($billing->getLastname()))
                ->setXCompany(strval($billing->getCompany()))
                ->setXAddress(strval($billing->getStreet(1)))
                ->setXCity(strval($billing->getCity()))
                ->setXState(strval($billing->getRegion()))
                ->setXZip(strval($billing->getPostcode()))
                ->setXCountry(strval($billing->getCountry()))
                ->setXPhone(strval($billing->getTelephone()))
                ->setXFax(strval($billing->getFax()))
                ->setXEmail(strval($order->getCustomerEmail()));
        }
        $shipping = $order->getShippingAddress();
        if (!empty($shipping)) {
            $this->setXShipToFirstName(strval($shipping->getFirstname()))
                ->setXShipToLastName(strval($shipping->getLastname()))
                ->setXShipToCompany(strval($shipping->getCompany()))
                ->setXShipToAddress(strval($shipping->getStreet(1)))
                ->setXShipToCity(strval($shipping->getCity()))
                ->setXShipToState(strval($shipping->getRegion()))
                ->setXShipToZip(strval($shipping->getPostcode()))
                ->setXShipToCountry(strval($shipping->getCountry()));
        }

        $message = '^';
        foreach ($hashFields as $field) {
            $fieldData = $this->getData($field);
            $message .= (isset($fieldData) ? $fieldData : '') . '^';
        }

        return strtoupper(hash_hmac('sha512', $message, pack('H*', $signatureKey)));
    }
}
