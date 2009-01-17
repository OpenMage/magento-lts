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
 * @version    $Id: Protocol.php 8230 2008-02-20 22:38:48Z thomas $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @category   Zend
 * @package    Zend_TimeSync
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_TimeSync_Protocol
{
    /**
     * Holds the current socket connection
     *
     * @var array
     */
    protected $_socket;

    /**
     * Exceptions that might have occured
     *
     * @var array
     */
    protected $_exceptions;

    /**
     * Hostname for timeserver
     *
     * @var string
     */
    protected $_timeserver;

    /**
     * Holds information passed/returned from timeserver
     *
     * @var array
     */
    protected $_info = array();

    /**
     * Abstract method that prepares the data to send to the timeserver
     * 
     * @return mixed
     */
    abstract protected function _prepare();
    
    /**
     * Abstract method that reads the data returned from the timeserver
     * 
     * @return mixed
     */
    abstract protected function _read();

    /**
     * Abstract method that writes data to to the timeserver
     * 
     * @return mixed
     */
    abstract protected function _write($data);

    /**
     * Abstract method that extracts the binary data returned from the timeserver
     * 
     * @return mixed
     */
    abstract protected function _extract($data);

    /**
     * Connect to the specified timeserver.
     * 
     * @return void
     * @throws Zend_TimeSync_Exception
     */
    protected function _connect()
    {
        $socket = @fsockopen($this->_timeserver, $this->_port, $errno, $errstr, Zend_TimeSync::$options['timeout']);
        if (!$socket) {
            throw new Zend_TimeSync_Exception("could not connect to " .
                "'$this->_timeserver' on port '$this->_port', reason: '$errstr'");
        }

        $this->_socket = $socket;
    }

    /**
     * Disconnects from the peer, closes the socket.
     * 
     * @return void
     */
    protected function _disconnect()
    {
        @fclose($this->_socket);
        $this->_socket = null;
    }

    /**
     * Return information sent/returned from the timeserver
     * 
     * @return  array
     */
    public function getInfo()
    {
        return $this->_info;
    }

    /**
     * Query this timeserver without using the fallback mechanism
     * 
     * @param   $locale optional locale
     * @return  Zend_Date
     * @throws  Zend_TimeSync_Exception
     */
    public function getDate($locale = null)
    {
        $this->_write($this->_prepare());
        $timestamp = $this->_extract($this->_read());

        return new Zend_Date($this, null, $locale);
    }
}
