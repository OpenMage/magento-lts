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
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


abstract class Mage_Core_Model_Resource_Entity_Abstract
{
    protected $_name = null;
    protected $_config = array();
    
    public function __construct($config)
    {
        $this->_config = $config;
    }
    
    public function getConfig($key='')
    {
        if (''===$key) {
        	return $this->_config;
        } elseif (isset($this->_config->$key)) {
        	return $this->_config->$key;
        } else {
            return false;
        }
    }    
}