<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Centinel
 */

/**
 * 3D Secure Validation Library for Payment
 */
include_once '3Dsecure/CentinelClient.php';

/**
 * 3D Secure Validation Api
 *
 * @package    Mage_Centinel
 */
class Mage_Centinel_Model_Api_Client extends CentinelClient
{
    public function sendHttp($url, $connectTimeout, $timeout)
    {
        // verify that the URL uses a supported protocol.
        if ((str_starts_with($url, 'http://')) || (str_starts_with($url, 'https://'))) {
            //Construct the payload to POST to the url.
            $data = $this->getRequestXml();

            // create a new cURL resource
            $ch = curl_init($url);

            // set URL and other appropriate options
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

            // Execute the request.
            $result = curl_exec($ch);
            $succeeded = curl_errno($ch) == 0;

            // close cURL resource, and free up system resources
            curl_close($ch);

            // If Communication was not successful set error result, otherwise
            if (!$succeeded) {
                $result = $this->setErrorResponse(CENTINEL_ERROR_CODE_8030, CENTINEL_ERROR_CODE_8030_DESC);
            }

            // Assert that we received an expected Centinel Message in response.
            if (!str_contains($result, '<CardinalMPI>')) {
                $result = $this->setErrorResponse(CENTINEL_ERROR_CODE_8010, CENTINEL_ERROR_CODE_8010_DESC);
            }
        } else {
            $result = $this->setErrorResponse(CENTINEL_ERROR_CODE_8000, CENTINEL_ERROR_CODE_8000_DESC);
        }

        $parser = new XMLParser();
        $parser->deserializeXml($result);

        $this->response = $parser->deserializedResponse;
    }
}
