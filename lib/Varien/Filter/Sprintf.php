<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Varien
 * @package    Varien_Filter
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2021-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Varien_Filter_Sprintf implements Zend_Filter_Interface
{
    protected $_format = null;
    protected $_decimals = null;
    protected $_decPoint = null;
    protected $_thousandsSep = null;

    public function __construct($format, $decimals = null, $decPoint = '.', $thousandsSep = ',')
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
