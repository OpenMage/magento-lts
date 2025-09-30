<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Media
 */

/**
 * Media library file image resource model
 *
 * @package    Mage_Media
 */
class Mage_Media_Model_File_Image extends Mage_Core_Model_Resource_Abstract
{
    /**
     * @return $this
     */
    protected function _construct()
    {
        return $this;
    }

    /**
     * @return Varien_Db_Adapter_Interface|false
     */
    protected function _getReadAdapter()
    {
        return false;
    }

    /**
     * @return Varien_Db_Adapter_Interface|false
     */
    protected function _getWriteAdapter()
    {
        return false;
    }

    /**
     * @param mixed $file
     * @param mixed|null $field
     * @return $this
     */
    public function load(Mage_Media_Model_Image $object, $file, $field = null)
    {
        // Do some implementation
        return $this;
    }

    /**
     * @return $this
     */
    public function save(Mage_Media_Model_Image $object)
    {
        // Do some implementation
        return $this;
    }

    /**
     * @return $this
     */
    public function delete(Mage_Media_Model_Image $object)
    {
        return $this;
    }

    /**
     * Create image resource for operation from file
     *
     * @return false|GdImage|resource
     * @throws Mage_Core_Exception
     */
    public function getImage(Mage_Media_Model_Image $object)
    {
        $resource = false;
        switch (strtolower($object->getExtension())) {
            case 'jpg':
            case 'jpeg':
                $resource = imagecreatefromjpeg($object->getFilePath());
                break;

            case 'webp':
                $resource = imagecreatefromwebp($object->getFilePath());
                break;

            case 'gif':
                $resource = imagecreatefromgif($object->getFilePath());
                break;

            case 'png':
                $resource = imagecreatefrompng($object->getFilePath());
                break;
        }

        if (!$resource) {
            Mage::throwException(Mage::helper('media')->__('The image does not exist or is invalid.'));
        }

        return $resource;
    }

    /**
     * Create tmp image resource for operations
     *
     * @return false|GdImage|resource
     */
    public function getTmpImage(Mage_Media_Model_Image $object)
    {
        return imagecreatetruecolor($object->getDestanationDimensions()->getWidth(), $object->getDestanationDimensions()->getHeight());
    }

    /**
     * Resize image
     *
     * @return $this
     */
    public function resize(Mage_Media_Model_Image $object)
    {
        $tmpImage = $object->getTmpImage();
        $sourceImage = $object->getImage();

        imagecopyresampled(
            $tmpImage,
            $sourceImage,
            0,
            0,
            0,
            0,
            $object->getDestanationDimensions()->getWidth(),
            $object->getDestanationDimensions()->getHeight(),
            $object->getDimensions()->getWidth(),
            $object->getDimensions()->getHeight(),
        );

        return $this;
    }

    /**
     * Add watermark for image
     *
     * @return $this
     */
    public function watermark(Mage_Media_Model_Image $object)
    {
        return $this;
    }

    /**
     * Creates image
     *
     * @param string|null $extension
     * @return $this
     */
    public function saveAs(Mage_Media_Model_Image $object, $extension = null)
    {
        if (is_null($extension)) {
            $extension = $object->getExtension();
        }

        $result = false;
        switch (strtolower($extension)) {
            case 'jpg':
            case 'jpeg':
                $result = imagejpeg($object->getTmpImage(), $object->getFilePath(true), 80);
                break;
            case 'webp':
                $result = imagewebp($object->getTmpImage(), $object->getFilePath(true), 80);
                break;
            case 'gif':
                $result = imagegif($object->getTmpImage(), $object->getFilePath(true));
                break;
            case 'png':
                $result = imagepng($object->getTmpImage(), $object->getFilePath(true));
                break;
        }

        if (!$result) {
            Mage::throwException(Mage::helper('media')->__('An error occurred while creating the image.'));
        }

        return $this;
    }

    /**
     * Retrieve image dimensions
     *
     * @return Varien_Object
     * @SuppressWarnings("PHPMD.ErrorControlOperator")
     */
    public function getDimensions(Mage_Media_Model_Image $object)
    {
        $info = @getimagesize($object->getFilePath());
        if (!$info) {
            Mage::throwException(Mage::helper('media')->__('The image does not exist or is invalid.'));
        }

        $info = ['width' => $info[0], 'height' => $info[1], 'type' => $info[2]];
        return new Varien_Object($info);
    }

    /**
     * Destroys resource object
     *
     * @param GdImage|resource $resource
     * @return Mage_Media_Model_File_Image
     */
    public function destroyResource(&$resource)
    {
        imagedestroy($resource);
        return $this;
    }

    /**
     * Destroys resource object
     *
     * @return bool
     */
    public function hasSpecialImage(Mage_Media_Model_Image $object)
    {
        if (file_exists($object->getFilePath(true))) {
            return true;
        }

        return false;
    }
}
