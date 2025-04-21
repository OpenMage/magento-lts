<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Payflow Advanced iframe block
 *
 * @package    Mage_Paypal
 */
class Mage_Paypal_Block_Payflow_Advanced_Form extends Mage_Paypal_Block_Payflow_Link_Form
{
    /**
     * Set payment method code
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('paypal/payflowadvanced/info.phtml');
    }

    /**
     * Get frame action URL
     *
     * @return string
     */
    public function getFrameActionUrl()
    {
        return $this->getUrl('paypal/payflowadvanced/form', ['_secure' => true]);
    }
}
