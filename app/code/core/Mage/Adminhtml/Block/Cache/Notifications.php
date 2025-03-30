<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Cache_Notifications extends Mage_Adminhtml_Block_Template
{
    /**
     * Get array of cache types which require data refresh
     *
     * @return array
     */
    public function getCacheTypesForRefresh()
    {
        $invalidatedTypes = Mage::app()->getCacheInstance()->getInvalidatedTypes();
        $res = [];
        foreach ($invalidatedTypes as $type) {
            $res[] = $type->getCacheType();
        }
        return $res;
    }

    /**
     * Get index management url
     *
     * @return string
     */
    public function getManageUrl()
    {
        return $this->getUrl('adminhtml/cache');
    }

    /**
     * ACL validation before html generation
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (Mage::getSingleton('admin/session')->isAllowed('system/cache')) {
            return parent::_toHtml();
        }
        return '';
    }
}
