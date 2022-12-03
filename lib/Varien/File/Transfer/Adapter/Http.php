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
 * @category   Varien
 * @package    Varien_File
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2016 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Csv parse
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Varien_File_Transfer_Adapter_Http
{
    protected $_mimeTypes = [
        'txt' => 'text/plain',
        'htm' => 'text/html',
        'html' => 'text/html',
        'php' => 'text/html',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'xml' => 'application/xml',
        'swf' => 'application/x-shockwave-flash',
        'flv' => 'video/x-flv',

        // images
        'png' => 'image/png',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'ico' => 'image/vnd.microsoft.icon',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',

        // archives
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        'exe' => 'application/x-msdownload',
        'msi' => 'application/x-msdownload',
        'cab' => 'application/vnd.ms-cab-compressed',

        // audio/video
        'mp3' => 'audio/mpeg',
        'qt' => 'video/quicktime',
        'mov' => 'video/quicktime',

        // adobe
        'pdf' => 'application/pdf',
        'psd' => 'image/vnd.adobe.photoshop',
        'ai' => 'application/postscript',
        'eps' => 'application/postscript',
        'ps' => 'application/postscript'
    ];

    /**
     * Send the file to the client (Download)
     *
     * @param  string|array $options Options for the file(s) to send
     * @throws Exception
     */
    public function send($options = null)
    {
        if (is_string($options)) {
            $filepath = $options;
        } elseif (is_array($options)) {
            $filepath = $options['filepath'];
        } else {
            throw new Exception("Filename is not set.");
        }

        if (!is_file($filepath) || !is_readable($filepath)) {
            throw new Exception("File '{$filepath}' does not exists.");
        }

        $mimeType = $this->_detectMimeType(['name' => $filepath]);

        $response = new Zend_Controller_Response_Http();

        $response->setHeader('Content-length', filesize($filepath));
        $response->setHeader('Content-Type', $mimeType);

        $response->sendHeaders();

        readfile($filepath);
    }

    /**
     * Internal method to detect the mime type of a file
     *
     * @param  array $value File infos
     * @return string Mime type of given file
     */
    protected function _detectMimeType($value)
    {
        if (file_exists($value['name'])) {
            $file = $value['name'];
        } elseif (file_exists($value['tmp_name'])) {
            $file = $value['tmp_name'];
        } else {
            return null;
        }

        $parts = explode('.', $file);
        $extention = strtolower(array_pop($parts));
        if (isset($this->_mimeTypes[$extention])) {
            $result = $this->_mimeTypes[$extention];
        }

        if (empty($result) && (function_exists('mime_content_type') && ini_get('mime_magic.magicfile'))) {
            $result = mime_content_type($file);
        }

        if (empty($result)) {
            $result = 'application/octet-stream';
        }

        return $result;
    }
}
