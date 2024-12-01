<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Downloadable
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require_once 'Mage/Downloadable/controllers/Adminhtml/Downloadable/Product/EditController.php';

/**
 * Adminhtml downloadable product edit
 *
 * @category   Mage
 * @package    Mage_Downloadable
 * @deprecated  after 1.4.2.0 Mage_Downloadable_Adminhtml_Downloadable_Product_EditController is used
 */
class Mage_Downloadable_Product_EditController extends Mage_Downloadable_Adminhtml_Downloadable_Product_EditController
{
    /**
     * Controller pre-dispatch method
     * Show 404 front page
     *
     * @return void
     */
    public function preDispatch()
    {
        $this->_forward('defaultIndex', 'cms_index');
    }
}
