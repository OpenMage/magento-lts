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
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tab design images block renderer
 *
 * @category    Mage
 * @package     Mage_Xmlconnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Design_Images extends Mage_Uploader_Block_Single
{
    /**
     * Init block, set preview template
     */
    public function __construct()
    {
        parent::__construct();

        $device = Mage::helper('xmlconnect')->getDeviceType();
        if (array_key_exists($device, Mage::helper('xmlconnect')->getSupportedDevices())) {
            $template = 'xmlconnect/edit/tab/design/images_' . strtolower($device) . '.phtml';
        } else {
            Mage::throwException($this->__('Device doesn\'t recognized. Unable to load a template.'));
        }

        $this->setTemplate($template);
    }

    /**
     * Get application id
     *
     * @return int
     */
    public function getApplicationId()
    {
        return Mage::helper('xmlconnect')->getApplicationId();
    }

    /**
     * Get image data array for uploader needs
     *
     * @param string $type
     * @param int $imageLimit
     * @return array
     */
    public function getImagesData($type, $imageLimit = 1)
    {
        if (!$this->{'get' . ucwords($type)}()) {
            /** @var $imagesModel Mage_XmlConnect_Model_Images */
            $imagesModel = Mage::getModel('xmlconnect/images');
            $this->setImageCount($imagesModel->getImageCount($type));
            $result = $imagesModel->getDeviceImagesByType($type, $imageLimit);
            $imageCount = count($result);
            $this->setIsShowUploder(true);
            if ($imageCount < $imageLimit) {
                $result[] = array('image_type' => $type, 'order' => ++$imageCount,
                    'application_id' => $this->getApplicationId(), 'show_uploader' => (int)$this->getIsShowUploder());
                $this->setIsShowUploder(false);
            }
            $this->{'set' . ucwords($type)}($result);
        }
        return $this->{'get' . ucwords($type)}();
    }

    /**
     * Get image list by type and limit
     *
     * @param string $imageType
     * @param int $count
     * @return array
     */
    public function getImageList($imageType, $count)
    {
        $imageList = $this->getImagesData($imageType, $count);
        $result = array();
        foreach ($imageList as $image) {
            $result[] = $this->_prepareImagesData($image);
        }
        return $result;
    }

    /**
     * Prepare image data for uploader
     *
     * @param array $image
     * @return array
     */
    protected function _prepareImagesData($image)
    {
        $this->clearConfig();
        $params = array('image_type' => $image['image_type'], '_secure' => true, 'order' => $image['order'],
            'application_id' => $this->getApplicationId());

        if (isset($image['image_id'])) {
            $this->getMiscConfig()->setData('file_save',
                Mage::getModel('xmlconnect/images')->getImageUrl($image['image_file']))
                    ->setImageId($image['image_id']
            )->setData('thumbnail',
                Mage::getModel('xmlconnect/images')->getCustomSizeImageUrl(
                $image['image_file'],
                Mage_XmlConnect_Helper_Data::THUMBNAIL_IMAGE_WIDTH,
                Mage_XmlConnect_Helper_Data::THUMBNAIL_IMAGE_HEIGHT
            ))->setData('image_id', $image['image_id']);

            $imageActionData = Mage::helper('xmlconnect')->getApplication()->getImageActionModel()
                ->getImageActionData($image['image_id']);
            if ($imageActionData) {
                $this->getMiscConfig()->setData('image_action_data', $imageActionData);
            }
        }

        $this->getUploaderConfig()
            ->setFileParameterName($image['image_type'])
            ->setTarget(
                Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('*/*/uploadimages', $params)
            );

        $this->getButtonConfig()
            ->setAttributes(
                array('accept' => $this->getButtonConfig()->getMimeTypesByExtensions('gif, jpg, jpeg, png'))
            );
        $this->getMiscConfig()
            ->setReplaceBrowseWithRemove(true)
            ->setData('image_count', $this->getImageCount())
        ;

        return parent::getJsonConfig();
    }

    /**
     * Prepare layout, change button and set front-end element ids mapping
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->_addElementIdsMapping(array(
            'container'     => $this->getHtmlId() . '-new',
            'idToReplace'   => $this->getHtmlId(),
        ));

        return $this;
    }

    /**
     * Retrieve config json
     *
     * @param array $image
     * @return string
     */
    public function getConfigJson($image)
    {
        return Mage::helper('core')->jsonEncode($this->_prepareImagesData($image));
    }

    /**
     * Retrieve image config object
     *
     * @deprecated
     * @return $this
     */
    public function getConfig()
    {
        return $this;
    }

    /**
     * Clear Image config object
     *
     * @return Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Design_Images
     */
    public function clearConfig()
    {
        $this->getMiscConfig()
            ->unsetData('image_id')
            ->unsetData('file_save')
            ->unsetData('thumbnail')
            ->unsetData('image_count')
        ;
        $this->getUploaderConfig()->unsetFileParameterName();
        return $this;
    }
}
