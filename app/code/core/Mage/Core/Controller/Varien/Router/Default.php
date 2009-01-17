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

class Mage_Core_Controller_Varien_Router_Default extends Mage_Core_Controller_Varien_Router_Abstract
{
    public function match(Zend_Controller_Request_Http $request)
    {
        $d = explode('/', Mage::app()->getStore()->getConfig('web/default/no_route'));
        $request->setModuleName(isset($d[0]) ? $d[0] : 'core')
            ->setControllerName(isset($d[1]) ? $d[1] : 'index')
            ->setActionName(isset($d[2]) ? $d[2] : 'index');

        return true;
    }
    /*
    public function getUrl($routeName, $params)
    {
        return 'no-route';
    }
    */

}