<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Route.php 1847 2006-11-23 11:36:41Z martel $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Controller_Router_Route_Interface */
#require_once 'Zend/Controller/Router/Route/Interface.php';

/**
 * Abstract Route
 *
 * Implements interface and provides convenience methods
 *
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Controller_Router_Route_Abstract implements Zend_Controller_Router_Route_Interface
{

    public function getVersion() {
        return 2;
    }
    
    public function chain(Zend_Controller_Router_Route_Interface $route, $separator = '/')
    {
        #require_once 'Zend/Controller/Router/Route/Chain.php';

        $chain = new Zend_Controller_Router_Route_Chain();
        $chain->chain($this)->chain($route, $separator);

        return $chain;
    }

}
