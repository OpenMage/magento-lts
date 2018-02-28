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
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tab design preview renderer
 *
 * @category     Mage
 * @package      Mage_Xmlconnect
 * @author       Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Design_Preview extends Mage_Adminhtml_Block_Template
{
    /**
     * Set preview template
     */
    public function __construct()
    {
        parent::__construct();

        $device = Mage::helper('xmlconnect')->getDeviceType();
        if (array_key_exists($device, Mage::helper('xmlconnect')->getSupportedDevices())) {
            $template = 'xmlconnect/edit/tab/design/preview_' . strtolower($device) . '.phtml';
        } else {
            Mage::throwException($this->__('Device doesn\'t recognized. Unable to load a template.'));
        }

        $this->setTemplate($template);
    }

    /**
     * Retieve preview action url
     *
     * @param string $page
     * @return string
     */
    public function getPreviewActionUrl($page = 'home')
    {
        $params = array();
        $model  = Mage::helper('xmlconnect')->getApplication();
        if ($model !== null) {
            if ($model->getId() !== null) {
                $params = array('application_id' => $model->getId());
            } else {
                $params = array('devtype' => $model->getType());
            }
        }
        return $this->getUrl('*/*/preview' . $page, $params);
    }
}
