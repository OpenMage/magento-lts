<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_SalesRule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * SalesRule Mass Coupon Generator
 *
 * @category   Mage
 * @package    Mage_SalesRule
 *
 * @method Mage_SalesRule_Model_Resource_Coupon getResource()
 *
 * @method string getDash()
 * @method string getFormat()
 * @method string getLength()
 * @method $this setLength(int $value)
 * @method int getMaxAttempts()
 * @method int getMaxProbability()
 * @method string getPrefix()
 * @method int getQty()
 * @method int getRuleId()
 * @method string getSuffix()
 * @method string getToDate()
 * @method int getUsesPerCoupon()
 * @method int getUsesPerCustomer()
 */
class Mage_SalesRule_Model_Coupon_Massgenerator extends Mage_Core_Model_Abstract implements Mage_SalesRule_Model_Coupon_CodegeneratorInterface
{
    /**
     * Maximum probability of guessing the coupon on the first attempt
     */
    public const MAX_PROBABILITY_OF_GUESSING = 0.25;
    public const MAX_GENERATE_ATTEMPTS = 10;

    /**
     * Count of generated Coupons
     * @var int
     */
    protected $_generatedCount = 0;

    /**
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('salesrule/coupon');
    }

    /**
     * Generate coupon code
     *
     * @return string
     */
    public function generateCode()
    {
        $format  = $this->getFormat();
        if (!$format) {
            $format = Mage_SalesRule_Helper_Coupon::COUPON_FORMAT_ALPHANUMERIC;
        }
        $length  = max(1, (int) $this->getLength());
        $split   = max(0, (int) $this->getDash());
        $suffix  = $this->getSuffix();
        $prefix  = $this->getPrefix();

        $splitChar = $this->getDelimiter();
        $charset = Mage::helper('salesrule/coupon')->getCharset($format);

        $code = '';
        $charsetSize = count($charset);
        for ($i = 0; $i < $length; $i++) {
            $char = $charset[random_int(0, $charsetSize - 1)];
            if ($split > 0 && ($i % $split) == 0 && $i != 0) {
                $char = $splitChar . $char;
            }
            $code .= $char;
        }
        return $prefix . $code . $suffix;
    }

    /**
     * Retrieve delimiter
     *
     * @return string
     */
    public function getDelimiter()
    {
        if ($this->getData('delimiter')) {
            return $this->getData('delimiter');
        } else {
            return Mage::helper('salesrule/coupon')->getCodeSeparator();
        }
    }

    /**
     * Generate Coupons Pool
     *
     * @return $this
     */
    public function generatePool()
    {
        $this->_generatedCount = 0;
        $size = $this->getQty();

        $maxProbability = $this->getMaxProbability() ? $this->getMaxProbability() : self::MAX_PROBABILITY_OF_GUESSING;
        $maxAttempts = $this->getMaxAttempts() ? $this->getMaxAttempts() : self::MAX_GENERATE_ATTEMPTS;

        /** @var Mage_SalesRule_Model_Coupon $coupon */
        $coupon = Mage::getModel('salesrule/coupon');

        $chars = count(Mage::helper('salesrule/coupon')->getCharset($this->getFormat()));
        $length = (int) $this->getLength();
        $maxCodes = $chars ** $length;
        $probability = $size / $maxCodes;
        //increase the length of Code if probability is low
        if ($probability > $maxProbability) {
            do {
                $length++;
                $maxCodes = $chars ** $length;
                $probability = $size / $maxCodes;
            } while ($probability > $maxProbability);
            $this->setLength($length);
        }

        $now = $this->getResource()->formatDate(
            Mage::getSingleton('core/date')->gmtTimestamp(),
        );

        for ($i = 0; $i < $size; $i++) {
            $attempt = 0;
            do {
                if ($attempt >= $maxAttempts) {
                    Mage::throwException(Mage::helper('salesrule')->__('Unable to create requested Coupon Qty. Please check settings and try again.'));
                }
                $code = $this->generateCode();
                $attempt++;
            } while ($this->getResource()->exists($code));

            $expirationDate = $this->getToDate();
            if ($expirationDate instanceof Zend_Date) {
                $expirationDate = $expirationDate->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
            }

            $coupon->setId(null)
                ->setRuleId($this->getRuleId())
                ->setUsageLimit($this->getUsesPerCoupon())
                ->setUsagePerCustomer($this->getUsesPerCustomer())
                ->setExpirationDate($expirationDate)
                ->setCreatedAt($now)
                ->setType(Mage_SalesRule_Helper_Coupon::COUPON_TYPE_SPECIFIC_AUTOGENERATED)
                ->setCode($code)
                ->save();

            $this->_generatedCount++;
        }
        return $this;
    }

    /**
     * Validate input
     *
     * @param array $data
     * @return bool
     */
    public function validateData($data)
    {
        return !empty($data) && !empty($data['qty']) && !empty($data['rule_id'])
            && !empty($data['length']) && !empty($data['format'])
            && (int) $data['qty'] > 0 && (int) $data['rule_id'] > 0
            && (int) $data['length'] > 0;
    }

    /**
     * Retrieve count of generated Coupons
     *
     * @return int
     */
    public function getGeneratedCount()
    {
        return $this->_generatedCount;
    }
}
