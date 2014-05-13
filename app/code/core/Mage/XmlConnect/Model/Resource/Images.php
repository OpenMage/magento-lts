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
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect Images resource model
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Resource_Images extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize connection and define resource
     *
     */
    protected function _construct()
    {
        $this->_init('xmlconnect/images', 'image_id');
    }

    /**
     * Repair image order for application by type
     *
     * @param Mage_Core_Model_Abstract $image
     * @return Mage_XmlConnect_Model_Resource_Images
     */
    public function repairOrder(Mage_Core_Model_Abstract $image)
    {
        $bind = array(':application_id' => (int)$image->getApplicationId(), ':image_type' => $image->getImageType());

        $select = $this->_getWriteAdapter()->select()->from($this->getMainTable(), array('image_id'))
            ->where('application_id=:application_id AND image_type=:image_type')
            ->order('order', Varien_Data_Collection::SORT_ORDER_ASC);

        $result = $this->_getWriteAdapter()->fetchCol($select, $bind);
        $imageModel = Mage::getModel('xmlconnect/images');
        $i = 0;
        foreach ($result as $image_id) {
            $imageModel->load($image_id)->setOrder(++$i)->save();
        }
        return $this;
    }

    /**
     * Save image data
     *
     * @param int $applicationId
     * @param string $imageFile
     * @param string $imageType
     * @param string $order
     * @return Mage_XmlConnect_Model_Resource_Images
     */
    public function saveImage($applicationId, $imageFile, $imageType, $order)
    {
        $newData = array(
            'application_id' => $applicationId,
            'image_file'    => $imageFile,
            'image_type'    => $imageType,
            'order'     => $order
        );

        $this->_getWriteAdapter()->insert($this->getMainTable(), $newData);
        return $this;
    }
}
