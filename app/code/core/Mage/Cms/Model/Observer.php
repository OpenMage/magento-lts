<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Cms
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * CMS Observer model
 *
 * @package    Mage_Cms
 */
class Mage_Cms_Model_Observer
{
    /**
     * Modify No Route Forward object
     *
     * @return $this
     */
    public function noRoute(Varien_Event_Observer $observer)
    {
        $observer->getEvent()->getStatus()
            ->setLoaded(true)
            ->setForwardModule('cms')
            ->setForwardController('index')
            ->setForwardAction('noRoute');
        return $this;
    }

    /**
     * Modify no Cookies forward object
     *
     * @return $this
     */
    public function noCookies(Varien_Event_Observer $observer)
    {
        $redirect = $observer->getEvent()->getRedirect();

        $pageId  = Mage::getStoreConfig(Mage_Cms_Helper_Page::XML_PATH_NO_COOKIES_PAGE);
        $pageUrl = Mage::helper('cms/page')->getPageUrl($pageId);

        if ($pageUrl) {
            $redirect->setRedirectUrl($pageUrl);
        } else {
            $redirect->setRedirect(true)
                ->setPath('cms/index/noCookies')
                ->setArguments([]);
        }
        return $this;
    }
}
