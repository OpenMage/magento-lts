<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Centinel
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2021 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * 3D Secure Validation Library for Payment
 */
include_once '3Dsecure/CentinelClient.php';

/**
 * 3D Secure Validation Api
 *
 * @category   Mage
 * @package    Mage_Centinel
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Centinel_Model_Api_Client extends CentinelClient
{
    /**
     * @param string $url
     * @param null $connectTimeout
     * @param mixed $timeout
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function sendHttp($url, $connectTimeout, $timeout)
    {
        // verify that the URL uses a supported protocol.
        if ((strpos($url, "http://") === 0) || (strpos($url, "https://") === 0)) {
            //Construct the payload to POST to the url.
            $data = $this->getRequestXml();

            // create a new cURL resource
            $ch = curl_init($url);

            // set URL and other appropriate options
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
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

            // Assert that we received an expected Centinel Message in reponse.
            if (strpos($result, "<CardinalMPI>") === false) {
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
