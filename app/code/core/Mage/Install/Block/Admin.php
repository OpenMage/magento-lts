<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Install
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2021-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Administrator account install block
 *
 * @category   Mage
 * @package    Mage_Install
 */
class Mage_Install_Block_Admin extends Mage_Install_Block_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('install/create_admin.phtml');
    }

    public function getPostUrl()
    {
        return $this->getUrl('*/*/administratorPost');
    }

    public function getFormData()
    {
        $data = $this->getData('form_data');
        if (is_null($data)) {
            $data = new Varien_Object(Mage::getSingleton('install/session')->getAdminData(true));
            $this->setData('form_data', $data);
        }
        return $data;
    }

    /**
     * Retrieve minimum length of admin password
     *
     * @return int
     */
    public function getMinAdminPasswordLength()
    {
        return Mage::getModel('admin/user')->getMinAdminPasswordLength();
    }
}
