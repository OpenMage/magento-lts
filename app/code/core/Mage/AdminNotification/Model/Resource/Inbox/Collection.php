<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_AdminNotification
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * AdminNotification Inbox model
 *
 * @category   Mage
 * @package    Mage_AdminNotification
 */
class Mage_AdminNotification_Model_Resource_Inbox_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource collection initialization
     *
     */
    protected function _construct()
    {
        $this->_init('adminnotification/inbox');
    }

    /**
     * Add remove filter
     *
     * @return $this
     */
    public function addRemoveFilter()
    {
        $this->getSelect()
            ->where('is_remove=?', 0);
        return $this;
    }
}
