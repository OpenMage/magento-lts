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
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Home banners list renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Homebanners extends Mage_Core_Block_Abstract
{
    /**
     * List of images separated by device those have to be shown on home banners page
     *
     * @return array
     */
    private function getBannerTypeArray()
    {
        return array(
            Mage_XmlConnect_Helper_Data::DEVICE_TYPE_ANDROID => array(
                Mage_XmlConnect_Model_Device_Android::IMAGE_TYPE_PORTRAIT_BANNER
            ),
            Mage_XmlConnect_Helper_Data::DEVICE_TYPE_IPHONE => array(
                Mage_XmlConnect_Model_Device_Iphone::IMAGE_TYPE_PORTRAIT_BANNER,
            ),
            Mage_XmlConnect_Helper_Data::DEVICE_TYPE_IPAD => array(
                Mage_XmlConnect_Model_Device_Ipad::IMAGE_TYPE_LANDSCAPE_BANNER,
                Mage_XmlConnect_Model_Device_Ipad::IMAGE_TYPE_PORTRAIT_BANNER,
        ));
    }

    /**
     * Render home banners list xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var $homeBannersXmlObj Mage_XmlConnect_Model_Simplexml_Element */
        $homeBannersXmlObj = Mage::getModel('xmlconnect/simplexml_element', '<home_banners></home_banners>');

        /** @var $deviceHelper Mage_XmlConnect_Helper_Data */
        $deviceHelper = Mage::helper('xmlconnect');
        /** @var $imagesModel Mage_XmlConnect_Model_Images */
        $imagesModel = Mage::getModel('xmlconnect/images');

        $bannerTypeCollection = $this->getBannerTypeArray();
        $deviceType = $deviceHelper->getDeviceType();
        foreach ($bannerTypeCollection[$deviceType] as $bannerType) {

            $bannerImageCollection = $imagesModel->getDeviceImagesByType($bannerType);

            foreach ($bannerImageCollection as $bannerImage) {
                $itemXmlObj = $homeBannersXmlObj->addCustomChild('item', null, array(
                    'entity_id' => $bannerImage['image_id'],
                    'type' => $bannerType
                ));

                $originalFile = Mage_XmlConnect_Model_Images::getBasePath($bannerImage['image_file']);
                $bannerUrl = $imagesModel->getScreenSizeImageUrlByType($bannerImage['image_file'], $bannerType);

                $itemXmlObj->addCustomChild('image', $bannerUrl, array(
                    'modification_time' => filemtime($originalFile)
                ));
                $this->_addImageAction($itemXmlObj, $bannerImage['image_id']);
            }
        }

        return $homeBannersXmlObj->asNiceXml();
    }

    /**
     * Add action info to xml
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $imageXml
     * @param int $imageId
     * @return Mage_XmlConnect_Block_Homebanners
     */
    protected function _addImageAction(Mage_XmlConnect_Model_Simplexml_Element $imageXml, $imageId)
    {
        $imageActionData = Mage::helper('xmlconnect')->getApplication()->getImageActionModel()
            ->getImageActionData($imageId);

        if (empty($imageActionData)) {
            return $this;
        }

        switch ($imageActionData['action_type']) {
            case Mage_XmlConnect_Model_ImageAction::ACTION_TYPE_CMS:
                $page = Mage::getModel('cms/page')->setStoreId(Mage::app()->getStore()->getId())
                    ->load($imageActionData['entity_action'], 'identifier');
                if ($page->getId()) {
                    $actionXml = $imageXml->addCustomChild('action', null, array(
                        'type' => $imageActionData['action_type']
                    ));
                    $actionXml->addCustomChild('attribute', $imageActionData['entity_action'], array(
                        'name' => 'id',
                    ));
                    $actionXml->addCustomChild('attribute', $page->getTitle(), array(
                        'name' => 'title'
                    ));
                }
                break;
            case Mage_XmlConnect_Model_ImageAction::ACTION_TYPE_PRODUCT:
                $product = Mage::getModel('catalog/product')->load($imageActionData['entity_action']);
                if ($product->getId()) {
                    $actionXml = $imageXml->addCustomChild('action', null, array(
                        'type' => $imageActionData['action_type']
                    ));
                    $actionXml->addCustomChild('attribute', $imageActionData['entity_action'], array(
                        'name' => 'id',
                    ));
                }
                break;
            case Mage_XmlConnect_Model_ImageAction::ACTION_TYPE_CATEGORY:
                $category = Mage::getModel('catalog/category')->load($imageActionData['entity_action']);
                if ($category->getEntityId()) {
                    $actionXml = $imageXml->addCustomChild('action', null, array(
                        'type' => $imageActionData['action_type']
                    ));
                    $actionXml->addCustomChild('attribute', $imageActionData['entity_action'], array(
                        'name' => 'id',
                    ));
                }
                break;
            default:
                Mage::throwException($this->__('Action type doesn\'t recognized.'));
                break;
        }
        return $this;
    }
}
