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
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect Images model
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_ImageAction extends Mage_Core_Model_Abstract
{

    /**
     * Default image action configuration category
     */
    const IMAGE_ACTION_CATEGORY_DEFAULT = 'image_action';

    /**
     * CMS action type
     */
    const ACTION_TYPE_CMS = 'cms';

    /**
     * Product action type
     */
    const ACTION_TYPE_PRODUCT = 'product';

    /**
     * Category action type
     */
    const ACTION_TYPE_CATEGORY = 'category';

    /**
     * Image action types
     *
     * @var array
     */
    protected $_imageActionTypes = array(self::ACTION_TYPE_CMS, self::ACTION_TYPE_PRODUCT, self::ACTION_TYPE_CATEGORY);

    /**
     * Required image action fields separated by action type
     *
     * @var array
     */
    protected $_requiredImageActionFields = array(
        self::ACTION_TYPE_CMS => array('action_type','image_id', 'entity_action', 'entity_name'),
        self::ACTION_TYPE_PRODUCT => array('action_type', 'image_id', 'entity_action', 'entity_name'),
        self::ACTION_TYPE_CATEGORY => array('action_type', 'image_id', 'entity_action', 'entity_name')
    );

    /**
     * Image action config path template
     *
     * @var string
     */
    protected $_imageActionConfigPathTemplate = 'image_id/%d/app_id/%d';

    /**
     * Image action data to save
     *
     * @var array
     */
    protected $_currentImageActionData;

    /**
     * Application model
     *
     * @var Mage_XmlConnect_Model_Application
     */
    protected $_applicationModel;

    /**
     * Initialize model
     *
     * @param null|Mage_XmlConnect_Model_Application $applicationModel
     */
    public function __construct($applicationModel = null)
    {
        $this->_setApplicationModel($applicationModel);
    }

    /**
     * Set application model
     *
     * @param Mage_XmlConnect_Model_Application $applicationModel
     * @return Mage_XmlConnect_Model_Device_Abstract
     */
    protected function _setApplicationModel($applicationModel = null)
    {
        if ($applicationModel instanceof Mage_XmlConnect_Model_Application) {
            $this->_applicationModel = $applicationModel;
        } else {
            $this->_applicationModel = Mage::helper('xmlconnect')->getApplication();
        }
        return $this;
    }

    /**
     * Get application model
     *
     * @return Mage_XmlConnect_Model_Application
     */
    protected function _getApplicationModel()
    {
        if (null === $this->_applicationModel) {
            $this->setApplicationModel(Mage::helper('xmlconnect')->getApplication());
        }
        return $this->_applicationModel;
    }


    /**
     * Save image action info
     *
     * @param array $imageData
     * @param string $category
     * @return Mage_XmlConnect_Model_Device_Abstract
     */
    public function saveImageAction($imageData, $category = self::IMAGE_ACTION_CATEGORY_DEFAULT)
    {
        $this->_setCurrentImageActionData(null)->_validateImagePrepareActionData($imageData);
        $actionData = $this->getCurrentImageActionData();
        $configPath = $this->_createImageActionConfigPath($actionData['image_id']);
        $this->_getApplicationModel()->getConfigModel()->saveConfig($this->_getApplicationModel()->getId(),
            array($configPath => serialize($actionData)), $category);
        return $this;
    }

    /**
     * Validate image action by action type and prepare data for save
     *
     * @throws Mage_Core_Exception
     * @param array $imageData
     * @return bool
     */
    protected  function _validateImagePrepareActionData($imageData)
    {
        if (isset($imageData['action_type']) && in_array($imageData['action_type'], $this->_imageActionTypes)) {
            if ($imageData['action_type'] == self::ACTION_TYPE_CMS) {
                $imageData['entity_name'] = Mage::getModel('cms/page')->load($imageData['entity_action'])->getTitle();
            }
            $result = array();
            foreach ($this->_requiredImageActionFields[$imageData['action_type']] as $requiredField) {
                if (empty($imageData[$requiredField])) {
                    Mage::throwException(Mage::helper('xmlconnect')->__('%s fields is required', $requiredField));
                }
                $result[$requiredField] = $imageData[$requiredField];
            }
            $this->_setCurrentImageActionData($result);
            return true;
        } else {
            Mage::throwException(Mage::helper('xmlconnect')->__('Action type does\'t recognized'));
            return false;
        }
    }

    /**
     * Create image action config path
     *
     * @param int $imageId
     * @return string
     */
    protected function _createImageActionConfigPath($imageId)
    {
        return sprintf($this->_imageActionConfigPathTemplate, $imageId, $this->_getApplicationModel()->getId());
    }

    /**
     * Set image action data
     *
     * @param mixed $data
     * @return Mage_XmlConnect_Model_Device_Abstract
     */
    protected function _setCurrentImageActionData($data)
    {
        $this->_currentImageActionData = $data;
        return $this;
    }

    /**
     * Action data for currently saved image
     *
     * @return array
     */
    public function getCurrentImageActionData()
    {
        return $this->_currentImageActionData;
    }

    /**
     * Get image action data
     *
     * @param int $imageId
     * @return mixed
     */
    public function getImageActionData($imageId)
    {
        $imageActionConfigData = $this->_getApplicationModel()->getConfigModel()->loadScalarValue(
            $this->_getApplicationModel()->getId(),
            self::IMAGE_ACTION_CATEGORY_DEFAULT,
            $this->_createImageActionConfigPath($imageId)
        );
        $imageActionData = $imageActionConfigData ? unserialize($imageActionConfigData) : $imageActionConfigData;
        return $this->_checkImageActionData($imageActionData);
    }

    /**
     * Check image action data
     *
     * @throws Mage_Core_Exception
     * @param array $imageActionData
     * @return mixed
     */
    protected function _checkImageActionData($imageActionData)
    {
        if (empty($imageActionData)) {
            return;
        }
        $storeId = Mage::app()->getStore()->getId();
        switch ($imageActionData['action_type']) {
            case self::ACTION_TYPE_CMS:
                $page = Mage::getModel('cms/page')->setStoreId($storeId)
                    ->load($imageActionData['entity_action'], 'identifier');
                if (!$page->getId() && $storeId == Mage_Core_Model_App::ADMIN_STORE_ID) {
                    $this->deleteAction($imageActionData['image_id']);
                    return;
                } elseif ($page->getId()) {
                    $imageActionData['entity_name'] = $page->getTitle();
                }
            break;
            case self::ACTION_TYPE_CATEGORY:
                $category = Mage::getModel('catalog/category')->load($imageActionData['entity_action']);
                if (!$category->getId()) {
                    $this->deleteAction($imageActionData['image_id']);
                    return;
                } elseif ($category->getIsActive() == 0 && Mage_Core_Model_App::ADMIN_STORE_ID != $storeId) {
                    return;
                } else {
                    $imageActionData['entity_name'] = $category->getName();
                }
            break;
            case self::ACTION_TYPE_PRODUCT:
                $product = Mage::getModel('catalog/product')->load($imageActionData['entity_action']);
                if (!$product->getId()) {
                    $this->deleteAction($imageActionData['image_id']);
                    return;
                } elseif ($product->getStatus() == Mage_Catalog_Model_Product_Status::STATUS_DISABLED
                    && Mage_Core_Model_App::ADMIN_STORE_ID != $storeId
                ) {
                    return;
                } else {
                    $imageActionData['entity_name'] = $product->getName();
                }
            break;
            default:
                Mage::throwException($this->__('Action type doesn\'t recognized.'));
                break;
        }
        return $imageActionData;
    }

    /**
     * Delete Image action
     *
     * @param int $imageId
     * @return Mage_XmlConnect_Model_ImageAction
     */
    public function deleteAction($imageId)
    {
        $this->_getApplicationModel()->getConfigModel()->deleteConfig($this->_getApplicationModel()->getId(),
            self::IMAGE_ACTION_CATEGORY_DEFAULT, $this->_createImageActionConfigPath($imageId));
        return $this;
    }

    /**
     * Save images new order
     *
     * @param array $imagesOrderData
     * @param string $type
     * @return Mage_XmlConnect_Model_ImageAction
     */
    public function saveImageOrder($imagesOrderData, $type)
    {
        if (empty($imagesOrderData)) {
            return $this;
        }
        /** @var $imagesModel Mage_XmlConnect_Model_Images */
        $imagesModel = Mage::getModel('xmlconnect/images');
        foreach ($imagesOrderData as $imageId => $imageOrder) {
            $imagesModel->load($imageId);
            if ($imagesModel->getImageId()) {
                $imagesModel->setOrder($imageOrder)->save();
            }
        }
        return $this;
    }
}
