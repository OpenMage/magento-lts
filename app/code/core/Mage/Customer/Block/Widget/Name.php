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

class Mage_Customer_Block_Widget_Name extends Mage_Customer_Block_Widget_Abstract
{
    public function _construct()
    {
        parent::_construct();

        // default template location
        $this->setTemplate('customer/widget/name.phtml');
    }

    /**
     * Can show config value
     *
     * @param string $key
     * @return bool
     */
    protected function _showConfig($key)
    {
        $value = $this->getConfig($key);
        if (empty($value)) {
            return false;
        }
        return true;
    }

    /**
     * Can show prefix
     *
     * @return bool
     */
    public function showPrefix()
    {
        return $this->_showConfig('prefix_show');
    }

    public function isPrefixRequired()
    {
        return $this->getConfig('prefix_show')=='req';
    }

    public function getPrefixOptions()
    {
        $options = trim($this->getConfig('prefix_options'));
        if (!$options) {
            return false;
        }
        $options = explode(';', $options);
        foreach ($options as &$v) {
            $v = $this->htmlEscape(trim($v));
        }
        return $options;
    }

    public function showMiddlename()
    {
        return $this->_showConfig('middlename_show');
    }

    public function showSuffix()
    {
        return $this->_showConfig('suffix_show');
    }

    public function isSuffixRequired()
    {
        return $this->getConfig('suffix_show') == 'req';
    }

    public function getSuffixOptions()
    {
        $options = trim($this->getConfig('suffix_options'));
        if (!$options) {
            return false;
        }
        $options = explode(';', $options);
        foreach ($options as &$v) {
            $v = $this->htmlEscape(trim($v));
        }
        return $options;
    }

    public function getClassName()
    {
        if (!$this->hasData('class_name')) {
            $this->setData('class_name', 'customer-name');
        }
        return $this->getData('class_name');
    }

    public function getContainerClassName()
    {
        $class = $this->getClassName();
        $class .= $this->showPrefix() ? '-prefix' : '';
        $class .= $this->showMiddlename() ? '-middlename' : '';
        $class .= $this->showSuffix() ? '-suffix' : '';
        return $class;
    }
}
