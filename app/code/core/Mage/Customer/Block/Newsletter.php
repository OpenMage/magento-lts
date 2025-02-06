<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Customer
 */

/**
 * Customer front  newsletter manage block
 *
 * @category   Mage
 * @package    Mage_Customer
 */
class Mage_Customer_Block_Newsletter extends Mage_Customer_Block_Account_Dashboard // Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('customer/form/newsletter.phtml');
    }

    /**
     * @return bool
     */
    public function getIsSubscribed()
    {
        return $this->getSubscriptionObject()->isSubscribed();
    }

    /**
     * @inheritDoc
     */
    public function getAction()
    {
        return $this->getUrl('*/*/save');
    }
}
