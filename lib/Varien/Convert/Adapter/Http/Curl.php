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

        // validate input parameter
        $validator = Validation::createValidator();
        $violations = $validator->validate($uri, [
            new Assert\NotBlank(),
            new Assert\Url(),
        ]);

        if ($violations->count() > 0) {
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
