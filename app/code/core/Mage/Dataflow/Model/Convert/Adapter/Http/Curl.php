<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Dataflow
 */

/**
 * Convert CURL HTTP adapter
 *
 * @category   Mage
 * @package    Mage_Dataflow
 */
class Mage_Dataflow_Model_Convert_Adapter_Http_Curl extends Mage_Dataflow_Model_Convert_Adapter_Abstract
{
    public function load()
    {
        // we expect <var name="uri">http://...</var>
        $uri = $this->getVar('uri');

        // validate input parameter
        if (!Zend_Uri::check($uri)) {
            $this->addException("Expecting a valid 'uri' parameter");
        }

        // use Varien curl adapter
        $http = new Varien_Http_Adapter_Curl();

        // send GET request
        $http->write('GET', $uri);

        // read the remote file
        $data = $http->read();

        $http->close();

        $data = preg_split('/^\r?$/m', $data, 2);
        $data = trim($data[1]);

        // save contents into container
        $this->setData($data);

        return $this;
    }

    public function save()
    {
        // no save implemented
        return $this;
    }
}
