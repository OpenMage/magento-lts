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
 * Adminhtml customers online grid block item renderer by ip.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Customer_Online_Grid_Renderer_Ip extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @SuppressWarnings(PHPMD.ErrorControlOperator)
     */
    public function render(Varien_Object $row)
    {
        /**
         * The output of the "inet_ntop" function was disabled to prevent an error throwing
         * in case when the database value is not an ipv6 or an ipv4 binary representation (ex. NULL).
         */
        return @inet_ntop($row->getData($this->getColumn()->getIndex()));
    }
}
