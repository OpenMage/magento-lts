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
 * @package     Mage_Install
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Common database config installation block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Install_Block_Db_Type extends Mage_Core_Block_Template
{
    /**
     * Db title
     *
     * @var string
     */
    protected $_title = null;

    /**
     * Return Db title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Retrieve configuration form data object
     *
     * @return Varien_Object
     */
    public function getFormData()
    {
        $data = $this->getData('form_data');
        if (is_null($data)) {
            $data = Mage::getSingleton('install/session')->getConfigData(true);
            if (empty($data)) {
                $data = Mage::getModel('install/installer_config')->getFormData();
            } else {
                $data = new Varien_Object($data);
            }
            $this->setFormData($data);
        }
        return $data;
    }
}
