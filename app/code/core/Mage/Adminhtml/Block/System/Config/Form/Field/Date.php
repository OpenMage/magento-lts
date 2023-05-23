<?php

declare(strict_types=1);

/**
 * OpenMage
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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml system config date field renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Config_Form_Field_Date extends Mage_Adminhtml_Block_System_Config_Form_Field_AbstractDate
{
    /**
     * @return Varien_Data_Form_Element_Date
     */
    protected function getDateClass(): Varien_Data_Form_Element_Date
    {
        return new Varien_Data_Form_Element_Date();
    }

    /**
     * @param string $format
     * @return string
     */
    protected function getDateFormat(string $format): string
    {
        return $this->getLocale()->getDateFormat($format);
    }

    /**
     * @return false
     */
    protected function isShowTime(): bool
    {
        return false;
    }
}
