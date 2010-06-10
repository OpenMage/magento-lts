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
 * @package     Mage_Customer
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer address helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Helper_Address extends Mage_Core_Helper_Abstract
{
    protected $_config;
    protected $_streetLines;
    protected $_formatTemplate = array();

    /**
     * Addresses url
     */
    public function getBookUrl()
    {

    }

    public function getEditUrl()
    {

    }

    public function getDeleteUrl()
    {

    }

    public function getCreateUrl()
    {

    }

    public function getRenderer($renderer)
    {
        if(is_string($renderer) && $className = Mage::getConfig()->getBlockClassName($renderer)) {
            return new $className();
        } else {
            return $renderer;
        }
    }

    public function getConfig($key, $store=null)
    {
        if (is_null($this->_config)) {
            $this->_config = Mage::getStoreConfig('customer/address');
        }
        return isset($this->_config[$key]) ? $this->_config[$key] : null;
    }

    public function getStreetLines($store=null)
    {
        if (is_null($this->_streetLines)) {
            $lines = $this->getConfig('street_lines', $store);
            $this->_streetLines = min(4, max(1, (int)$lines));
        }
        return $this->_streetLines;
    }

    public function getFormat($code)
    {
        $format = Mage::getSingleton('customer/address_config')->getFormatByCode($code);
        return $format->getRenderer() ? $format->getRenderer()->getFormat() : '';
    }

    /**
     * Determine if specified address config value can show
     *
     * @return bool
     */
    public function canShowConfig($key)
    {
        $value = $this->getConfig($key);
        if (empty($value)) {
            return false;
        }
        return true;
    }
}
