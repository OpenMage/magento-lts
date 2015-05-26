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
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect status field grid renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Grid_Renderer_Bool
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render application status image
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $result = '';
        $status = (int) $row->getData($this->getColumn()->getIndex());
        $options = Mage::helper('xmlconnect')->getStatusOptions();
        if ($status == Mage_XmlConnect_Model_Application::APP_STATUS_SUCCESS) {
            $result = '<img src="' . Mage::helper('xmlconnect/image')->getSkinImagesUrl('gel_green.png') . '" >&nbsp;'
                . (isset($options[$status]) ? $options[$status] : '');
        } else if ($status == Mage_XmlConnect_Model_Application::APP_STATUS_INACTIVE) {
            $result = '<img src="' . Mage::helper('xmlconnect/image')->getSkinImagesUrl('gel_red.png') . '" >&nbsp;'
            . (isset($options[$status]) ? $options[$status] : '');
        }
        return $result;
    }
}
