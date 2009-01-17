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
 * @category   Zend
 * @package    Zend_TimeSync
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Sntp.php 8230 2008-02-20 22:38:48Z thomas $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Zend_TimeSync_Protocol
 */
#require_once 'Zend/TimeSync/Protocol.php';

/**
 * @category   Zend
 * @package    Zend_TimeSync
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_TimeSync_Sntp extends Zend_TimeSync_Protocol
{
    /**
     * Port number for this timeserver
     *
     * @var int
     */
    protected $_port = 37;
    
    /**
     * Socket delay
     *
     * @var integer
     */
    private $_delay;
    
    /**
     * Class constructor, sets the timeserver and port number
     *
     * @param  string $timeserver
     * @param  int    $port
     * @return void
     */
    public function __construct($timeserver, $port)
    {
        $this->_timeserver = 'tcp://' . $timeserver;
        if (!is_null($port)) {
            $this->_port = $port;
        }
    }

    /**
     * Prepares the data that will be send to the timeserver
     * 
     * @return array
     */
    protected function _prepare()
    {
        return "\n";
    }

    /**
     * Reads the data returned from the timeserver
     * 
     * @return void
     */
    protected function _read()
    {
        $result = fread($this->_socket, 49);
        $this->_delay = ($this->_delay - time()) / 2;

        return $result;
    }

    /**
     * Writes data to to the timeserver
     * 
     * @param  array $data
     * @return void
     */
    protected function _write($data)
    {
        $this->_connect();
        $this->_delay = time();
        fputs($this->_socket, $data);
    }

    /**
     * Extracts the data returned from the timeserver
     * 
     * @param  array $result
     * @return integer
     */
    protected function _extract($result)
    {
        $time  = abs(hexdec('7fffffff') - hexdec(bin2hex($result)) - hexdec('7fffffff'));
        $time -= 2208988800;
        // socket delay
        $time -= $this->_delay;

        $this->_info['offset'] = $this->_delay;

        return $time;
    }
}
