<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Media
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Media library Image model
 *
 * @category   Mage
 * @package    Mage_Media
 *
 * @method string getFileName()
 * @method $this setFileName(string $value)
 */
class Mage_Media_Model_Image extends Mage_Core_Model_Abstract
{
    /**
     * Image config instance
     * @var Mage_Media_Model_Image_Config_Interface
     */
    protected $_config;

    /**
     * Image resource
     * @var resource|null
     */
    protected $_image;

    /**
     * Tmp image resource
     * @var resource|null
     */
    protected $_tmpImage;

    /**
     * Params for filename generation
     * @var array
     */
    protected $_params = [];

    protected function _construct()
    {
        $this->_init('media/image');
    }

    /**
     * Set media image config instance
     * @return Mage_Media_Model_Image
     */
    public function setConfig(Mage_Media_Model_Image_Config_Interface $config)
    {
        $this->_config = $config;
        return $this;
    }

    /**
     * Retrieve media image config instance
     * @return Mage_Media_Model_Image_Config_Interface
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * @return resource
     */
    public function getImage()
    {
        if (is_null($this->_image)) {
            $this->_image = $this->_getResource()->getImage($this);
        }

        return $this->_image;
    }

    /**
     * @return resource
     */
    public function getTmpImage()
    {
        if (is_null($this->_image)) {
            $this->_tmpImage = $this->_getResource()->getTmpImage($this);
        }

        return $this->_tmpImage;
    }

    /**
     * Retrieve source dimensions object
     *
     * @return Varien_Object
     */
    public function getDimensions()
    {
        if (!$this->getData('dimensions')) {
            $this->setData('dimensions', $this->_getResource()->getDimensions($this));
        }
        return $this->getData('dimensions');
    }

    /**
     * Retrieve destination dimensions object
     *
     * @return Varien_Object
     */
    public function getDestanationDimensions()
    {
        if (!$this->getData('destanation_dimensions')) {
            $this->setData('destanation_dimensions', clone $this->getDimensions());
        }

        return $this->getData('destanation_dimensions');
    }

    /**
     * @return bool|string
     */
    public function getExtension()
    {
        return substr($this->getFileName(), strrpos($this->getFileName(), '.') + 1);
    }

    /**
     * @param bool $useParams
     * @return string
     */
    public function getFilePath($useParams = false)
    {
        if ($useParams && count($this->getParams())) {
            $changes = '.' . $this->getParamsSum();
        } else {
            $changes = '';
        }

        return $this->getConfig()->getBaseMediaPath() . DS . $this->getName() . $changes . '.'
             . (($useParams && $this->getParam('extension')) ? $this->getParam('extension') : $this->getExtension());
    }

    /**
     * @param bool $useParams
     * @return string
     */
    public function getFileUrl($useParams = false)
    {
        if ($useParams && count($this->getParams())) {
            $changes = '.' . $this->getParamsSum();
        } else {
            $changes = '';
        }

        return $this->getConfig()->getBaseMediaUrl() . '/' . $this->getName() . $changes . '.'
             . (($useParams && $this->getParam('extension')) ? $this->getParam('extension') : $this->getExtension());
    }

    /**
     * @return bool|string
     */
    public function getName()
    {
        return substr($this->getFileName(), 0, strrpos($this->getFileName(), '.'));
    }

    /**
     * @param array|string $param
     * @param string $value
     * @return $this
     */
    public function addParam($param, $value = null)
    {
        if (is_array($param)) {
            $this->_params = array_merge($this->_params, $param);
        } else {
            $this->_params[$param] = $value;
        }

        return $this;
    }

    /**
     * @param array|string $param
     * @param string $value
     * @return $this
     */
    public function setParam($param, $value = null)
    {
        if (is_array($param)) {
            $this->_params = $param;
        } else {
            $this->_params[$param] = $value;
        }

        return $this;
    }

    /**
     * @param string $param
     * @return string|null
     */
    public function getParam($param)
    {
        return $this->_params[$param] ?? null;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * @return string
     */
    public function getParamsSum()
    {
        return md5(serialize($this->_params));
    }

    /**
     * Return special link (with creating image if not exists)
     *
     * @param string $file
     * @param string $size
     * @param string $extension
     * @param string $watermark
     * @return string
     */
    public function getSpecialLink($file, $size, $extension = null, $watermark = null)
    {
        $this->_removeResources();
        $this->setData([]);
        $this->setParam([]);
        $this->setFileName($file);

        $this->addParam('size', $size);
        $this->addParam('watermark', $watermark);
        $this->addParam('extension', $extension);

        if (!$this->hasSpecialImage()) {
            if (str_contains($size, 'x')) {
                list($width, $height) = explode('x', $size);
            } else {
                $width = $size;
                $height = $this->getDimensions()->getHeight();
            }

            $sizeHRate = $width / $this->getDimensions()->getWidth();
            $sizeVRate = $height / $this->getDimensions()->getHeight();

            $rate = min($sizeHRate, $sizeVRate);

            if ($rate > 1) { // If image smaller than needed
                $rate = 1;
            }

            $this->getDestanationDimensions()
                ->setWidth($rate * $this->getDimensions()->getWidth())
                ->setHeight($rate * $this->getDimensions()->getHeight());

            $this->_getResource()->resize($this);
            $this->_getResource()->watermark($this);
            $this->_getResource()->saveAs($this, $extension);
            $this->_removeResources();
        }

        return $this->getFileUrl(true);
    }

    /**
     * @return bool
     */
    public function hasSpecialImage()
    {
        return $this->_getResource()->hasSpecialImage($this);
    }

    protected function _removeResources()
    {
        if ($this->_image) {
            $this->_getResource()->destroyResource($this->_image);
            $this->_image = null;
        }

        if ($this->_tmpImage) {
            $this->_getResource()->destroyResource($this->_tmpImage);
            $this->_tmpImage = null;
        }
    }
}
