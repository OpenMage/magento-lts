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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Ideal
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * iDEAL Advanced Api Security Model
 *
 * @category    Mage
 * @package     Mage_Ideal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Ideal_Model_Api_Advanced_Security
{
    /**
    *  reads in a certificate file and creates a fingerprint
    *  @param Filename of the certificate
    *  @return fingerprint
    */
    function createCertFingerprint($filename)
    {
        if(is_readable($filename)) {
            $cert = file_get_contents($filename);
        } else {
            return false;
        }

        $data = openssl_x509_read($cert);

        if(!openssl_x509_export($data, $data)) {

            return false;
        }

        $data = str_replace("-----BEGIN CERTIFICATE-----", "", $data);
        $data = str_replace("-----END CERTIFICATE-----", "", $data);

        $data = base64_decode($data);

        $fingerprint = sha1($data);

        $fingerprint = strtoupper( $fingerprint );

        return $fingerprint;
    }

    /**
    * function to sign a message
    * @param filename of the private key
    * @param message to sign
    * @return signature
    */
    function signMessage($priv_keyfile, $key_pass, $data)
    {
        $data = preg_replace("/\s/","",$data);
        if (is_readable($priv_keyfile)) {
            $priv_key = file_get_contents($priv_keyfile);

            $params = array($priv_key, $key_pass);
            $pkeyid = openssl_pkey_get_private($params);

            // compute signature
            openssl_sign($data, $signature, $pkeyid);

            // free the key from memory
            openssl_free_key($pkeyid);

            return $signature;
        } else {
            return false;
        }
    }

    /**
    * function to verify a message
    * @param filename of the public key to decrypt the signature
    * @param message to verify
    * @param sent signature
    * @return signature
    */
    function verifyMessage($certfile, $data, $signature)
    {
        // $data and $signature are assumed to contain the data and the signature
        $ok = 0;
        if (is_readable($certfile)) {
            $cert = file_get_contents($certfile);
        } else {
            return false;
        }

        $pubkeyid = openssl_get_publickey($cert);

        // state whether signature is okay or not
        $ok = openssl_verify($data, $signature, $pubkeyid);

        // free the key from memory
        openssl_free_key($pubkeyid);

        return $ok;
    }

    /**
    * @param fingerprint that's been sent
    * @param the configuration file loaded in as an array
    * @return the filename of the certificate with this fingerprint
    */
    function getCertificateName($fingerprint, $config)
    {
        $count = 0;

        if (isset($config["CERTIFICATE" . $count])) {
            $certFilename = $config["CERTIFICATE" . $count];
        } else {
            return false;
        }

        while( isset($certFilename) ) {
            $buff = $this->createCertFingerprint($certFilename);

            if( $fingerprint == $buff ) {
                return $certFilename;
            }

            $count+=1;
            if (isset($config["CERTIFICATE" . $count])) {
                $certFilename = $config["CERTIFICATE" . $count];
            } else {
                return false;
            }
        }

        return false;
    }
}
