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
 * XmlConnect Tab items block
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Preview_Tabitems extends Mage_Adminhtml_Block_Template
{
    /**
     * Set preview tab items template
     */
    public function __construct()
    {
        parent::__construct();

        $deviceType = Mage::helper('xmlconnect')->getDeviceType();
        $this->setTemplate('xmlconnect/edit/tab/design/preview/tab_items_' . $deviceType . '.phtml');
    }

    /**
     * Set active tab
     *
     * @param string $tab
     * @return Mage_XmlConnect_Block_Adminhtml_Mobile_Preview_Tabitems
     */
    public function setActiveTab($tab)
    {
        Mage::helper('xmlconnect')->getPreviewModel()->setActiveTab($tab);
        return $this;
    }
}
