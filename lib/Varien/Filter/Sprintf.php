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
 * @category    Varien
 * @package     Varien_Filter
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Varien_Filter_Sprintf implements Zend_Filter_Interface
{
    protected $_format = null;
    protected $_decimals = null;
    protected $_decPoint = null;
    protected $_thousandsSep = null;
    
    public function __construct($format, $decimals=null, $decPoint='.', $thousandsSep=',')
    {
        $this->_format = $format;
        $this->_decimals = $decimals;
        $this->_decPoint = $decPoint;
        $this->_thousandsSep = $thousandsSep;
    }
    
    public function filter($value)
    {
        if (!is_null($this->_decimals)) {
            $value = number_format($value, $this->_decimals, $this->_decPoint, $this->_thousandsSep);
        }
        $value = sprintf($this->_format, $value);
        return $value;
    }
}
