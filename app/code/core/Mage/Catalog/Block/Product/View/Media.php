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
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Simple product data view
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Block_Product_View_Media extends Mage_Catalog_Block_Product_View_Abstract
{
    /**
     * Flag, that defines whether gallery is disabled
     *
     * @var boolean
     */
    protected $_isGalleryDisabled;

    /**
     * Retrieve list of gallery images
     *
     * @return array|Varien_Data_Collection
     */
    public function getGalleryImages()
    {
        if ($this->_isGalleryDisabled) {
            return array();
        }
        $collection = $this->getProduct()->getMediaGalleryImages();
        return $collection;
    }

    /**
     * Retrieve gallery url
     *
     * @param null|Varien_Object $image
     * @return string
     */
    public function getGalleryUrl($image = null)
    {
        $params = array('id' => $this->getProduct()->getId());
        if ($image) {
            $params['image'] = $image->getValueId();
        }
        return $this->getUrl('catalog/product/gallery', $params);
    }

    /**
     * Retrieve gallery image url
     *
     * @param null|Varien_Object $image
     * @return string
     */
    public function getGalleryImageUrl($image)
    {
        if ($image) {
            $helper = $this->helper('catalog/image')
                ->init($this->getProduct(), 'image', $image->getFile())
                ->keepFrame(false);

            $size = Mage::getStoreConfig(Mage_Catalog_Helper_Image::XML_NODE_PRODUCT_BASE_IMAGE_WIDTH);
            if (is_numeric($size)) {
                $helper->constrainOnly(true)->resize($size);
            }
            return (string)$helper;
        }
        return null;
    }

    /**
     * Retrieve visibility of gallery image based on gallery filter where present
     *
     * @param null|Varien_Object $image
     * @return bool
     */
    public function isGalleryImageVisible($image)
    {
        if (($filterClass = $this->getGalleryFilterHelper()) && ($filterMethod = $this->getGalleryFilterMethod())) {
            return Mage::helper($filterClass)->$filterMethod($this->getProduct(), $image);
        }
        return true;
    }

    /**
     * Disable gallery
     */
    public function disableGallery()
    {
        $this->_isGalleryDisabled = true;
    }
}
