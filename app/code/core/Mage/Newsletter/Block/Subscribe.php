<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Newsletter
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Newsletter subscribe block
 *
 * @category   Mage
 * @package    Mage_Newsletter
 */
class Mage_Newsletter_Block_Subscribe extends Mage_Core_Block_Template
{
    /**
     * @return string
     */
    public function getSuccessMessage()
    {
        return Mage::getSingleton('newsletter/session')->getSuccess();
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return Mage::getSingleton('newsletter/session')->getError();
    }

    /**
     * Retrieve form action url and set "secure" param to avoid confirm
     * message when we submit form from secure page to unsecure
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('newsletter/subscriber/new', ['_secure' => true]);
    }
}
