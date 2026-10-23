<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Convert
 */

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

/**
 * Convert CURL HTTP adapter
 *
 * @package    Varien_Convert
 */
class Varien_Convert_Adapter_Http_Curl extends Varien_Convert_Adapter_Abstract
{
    // load method
    public function load()
    {
        // we expect <var name="uri">http://...</var>
        $uri = $this->getVar('uri');
        $this->_validateUri($uri);

        // use Varien curl adapter
        $http = new Varien_Http_Adapter_Curl();
        $this->_restrictProtocols($http);

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

    /**
     * @param  mixed                    $uri
     * @throws Varien_Convert_Exception
     */
    protected function _validateUri($uri): void
    {
        // validate input parameter
        $validator = Validation::createValidator();
        $violations = $validator->validate($uri, [
            new Assert\NotBlank(),
            new Assert\Url(protocols: ['http', 'https']),
        ]);

        if ($violations->count() > 0) {
            throw $this->addException(
                "Expecting a valid 'uri' parameter with http or https protocol",
                Varien_Convert_Exception::FATAL,
            );
        }
    }

    protected function _restrictProtocols(Varien_Http_Adapter_Curl $http): void
    {
        $protocols = CURLPROTO_HTTP | CURLPROTO_HTTPS;

        if (defined('CURLOPT_PROTOCOLS')) {
            $http->addOption(CURLOPT_PROTOCOLS, $protocols);
        }

        if (defined('CURLOPT_REDIR_PROTOCOLS')) {
            $http->addOption(CURLOPT_REDIR_PROTOCOLS, $protocols);
        }
    }
}
