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
 * @category    Mage
 * @package     Mage_Connect
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
* Class frontend
*
* @category   Mage
* @package    Mage_Connect
* @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/
class Maged_Connect_Frontend extends Mage_Connect_Frontend
{

    /**
    * Log stream or not
    *
    * @var string
    */
    protected $_logStream = null;

    /**
    * Output cache
    *
    * @var array
    */
    protected $_out = array();

     /**
     * Set log stream
     *
     * @param string|resource $stream 'stdout' or open php stream
     */
    public function setLogStream($stream)
    {
        $this->_logStream = $stream;
        return $this;
    }

    /**
    * Retrieve log stream
    *
    * @return string
    */
    public function getLogStream()
    {
        return $this->_logStream;
    }

    /**
    * Echo data from executed command
    */
    public function output($data)
    {

        $this->_out = $data;

        if ('stdout'===$this->_logStream) {
            if (is_string($data)) {
                echo $data."<br/>".str_repeat(" ", 256);
            } elseif (is_array($data)) {
                $data = array_pop($data);
                if (!empty($data['message']) && is_string($data['message'])) {
                    echo $data['message']."<br/>".str_repeat(" ", 256);
                } elseif (!empty($data['data'])) {
                    if (is_string($data['data'])) {
                        echo $data['data']."<br/>".str_repeat(" ", 256);
                    } else {
                        if (isset($data['title'])) {
                            echo $data['title']."<br/>".str_repeat(" ", 256);
                        }
                        if (is_array($data['data'])) {
                            foreach ($data['data'] as $row) {
                                foreach ($row as $msg) {
                                    echo "&nbsp;".$msg;
                                }
                                echo "<br/>".str_repeat(" ", 256);
                            }
                        } else {
                            echo "&nbsp;".$data['data'];
                        }
                    }
                }
            } else {
                print_r($data);
            }
        }
    }

    /**
    * Method for ask client about rewrite all files.
    *
    * @param $string
    */
    public function confirm($string)
    {
        $confirmString = htmlentities($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $formId = htmlspecialchars($_POST['form_id'], ENT_COMPAT | ENT_HTML401, 'UTF-8');

        echo <<<SCRIPT
        <script type="text/javascript">
            if (confirm("{$confirmString}")) {
                parent.document.getElementById('ignore_local_modification').value=1;
                parent.onSuccess();
                if (parent && parent.disableInputs) {
                    parent.disableInputs(false);
                }
                window.onload = function () {
                    parent.document.getElementById('{$formId}').submit();
                    parent.document.getElementById('ignore_local_modification').value='';
                }
            }
        </script>
SCRIPT;
    }

    /**
     * Retrieve output cache
     *
     * @param bool $clearPrevious
     * @return array|mixed
     */
    public function getOutput($clearPrevious = false)
    {
        return $this->_out;
    }

}

