<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Newsletter
 */

/**
 * Newsletter subscribe block
 *
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
