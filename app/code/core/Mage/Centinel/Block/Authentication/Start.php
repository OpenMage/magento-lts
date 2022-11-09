<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Centinel
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Authentication start/redirect form
 *
 * @category   Mage
 * @package    Mage_Centinel
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Centinel_Block_Authentication_Start extends Mage_Core_Block_Template
{
    /**
     * Prepare form parameters and render
     * @return string
     */
    protected function _toHtml()
    {
        $validator = Mage::registry('current_centinel_validator');
        if ($validator && $validator->shouldAuthenticate()) {
            $this->addData($validator->getAuthenticateStartData());
            return parent::_toHtml();
        }
        return '';
    }
}

