<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

/**
 * Session trait
 *
 * @category   Mage
 * @package    Mage_Core
 */
trait Mage_Core_Trait_Session
{
    protected static string $sessionApi             = 'api/session';
    protected static string $sessionAdmin           = 'admin/session';
    protected static string $sessionAdminhtml       = 'adminhtml/session';
    protected static string $sessionCatalog         = 'catalog/session';
    protected static string $sessionCatalogSearch   = 'catalogsearch/session';
    protected static string $sessionCentinel        = 'centinal/session';
    protected static string $sessionCheckout        = 'checkout/session';
    protected static string $sessionCore            = 'core/session';
    protected static string $sessionCustomer        = 'customer/session';
    protected static string $sessionInstall         = 'install/session';
    protected static string $sessionNewsletter      = 'newsletter/session';
    protected static string $sessionPaypal          = 'paypal/session';
    protected static string $sessionReports         = 'reports/session';
    protected static string $sessionReview          = 'review/session';
    protected static string $sessionRss             = 'rss/session';
    protected static string $sessionTag             = 'tag/session';
    protected static string $sessionWishlist        = 'install/wishlist';

    /**
     * Retrieve admin session model object
     */
    final protected function getAdminSession(): Mage_Admin_Model_Session
    {
        /** @var Mage_Admin_Model_Session $session */
        $session = Mage::getSingleton(self::$sessionAdmin);
        return $session;
    }

    final protected function getAdminSessionStorage(): string
    {
        return self::$sessionAdmin;
    }

    /**
     * Retrieve adminhtml session model object
     */
    final protected function getAdminhtmlSession(): Mage_Adminhtml_Model_Session
    {
        /** @var Mage_Adminhtml_Model_Session $session */
        $session = Mage::getSingleton(self::$sessionAdminhtml);
        return $session;
    }

    /**
     * Retrieve catalog session model object
     */
    final protected function getApiSession(): Mage_Api_Model_Session
    {
        /** @var Mage_Api_Model_Session $session */
        $session = Mage::getSingleton(self::$sessionApi);
        return $session;
    }

    final protected function getApiSessionStorage(): string
    {
        return self::$sessionApi;
    }

    final protected function getAdminhtmlSessionStorage(): string
    {
        return self::$sessionAdminhtml;
    }

    /**
     * Retrieve catalog session model object
     */
    final protected function getCatalogSession(): Mage_Catalog_Model_Session
    {
        /** @var Mage_Catalog_Model_Session $session */
        $session = Mage::getSingleton(self::$sessionCatalog);
        return $session;
    }

    final protected function getCatalogSessionStorage(): string
    {
        return self::$sessionCatalog;
    }

    /**
     * Retrieve catalog session model object
     */
    final protected function getCatalogSearchSession(): Mage_CatalogSearch_Model_Session
    {
        /** @var Mage_CatalogSearch_Model_Session $session */
        $session = Mage::getSingleton(self::$sessionCatalogSearch);
        return $session;
    }

    final protected function getCatalogSearchSessionStorage(): string
    {
        return self::$sessionCatalogSearch;
    }

    /**
     * Retrieve centinel session model object
     */
    final protected function getCentinalSession(): Mage_Centinel_Model_Session
    {
        /** @var Mage_Centinel_Model_Session $session */
        $session = Mage::getSingleton(self::$sessionCentinel);
        return $session;
    }

    final protected function getCentinalSessionStorage(): string
    {
        return self::$sessionCentinel;
    }

    /**
     * Retrieve checkout session model object
     */
    final protected function getCheckoutSession(): Mage_Checkout_Model_Session
    {
        /** @var Mage_Checkout_Model_Session $session */
        $session = Mage::getSingleton(self::$sessionCheckout);
        return $session;
    }

    final protected function getCheckoutSessionStorage(): string
    {
        return self::$sessionCheckout;
    }

    /**
     * Retrieve core session model object
     */
    final protected function getCoreSession(): Mage_Core_Model_Session
    {
        /** @var Mage_Core_Model_Session $session */
        $session = Mage::getSingleton(self::$sessionCore);
        return $session;
    }

    final protected function getCoreSessionStorage(): string
    {
        return self::$sessionCore;
    }

    /**
     * Retrieve customer session model object
     */
    final protected function getCustomerSession(): Mage_Customer_Model_Session
    {
        /** @var Mage_Customer_Model_Session $session */
        $session = Mage::getSingleton(self::$sessionCustomer);
        return $session;
    }

    final protected function getCustomerSessionStorage(): string
    {
        return self::$sessionCustomer;
    }

    /**
     * Retrieve customer session model object
     */
    final protected function getInstallSession(): Mage_Install_Model_Session
    {
        /** @var Mage_Install_Model_Session $session */
        $session = Mage::getSingleton(self::$sessionInstall);
        return $session;
    }

    final protected function getInstallSessionStorage(): string
    {
        return self::$sessionInstall;
    }

    /**
     * Retrieve newsletter session model object
     */
    final protected function getNewsletterSession(): Mage_Newsletter_Model_Session
    {
        /** @var Mage_Newsletter_Model_Session $session */
        $session = Mage::getSingleton(self::$sessionNewsletter);
        return $session;
    }

    final protected function getNewsletterSessionStorage(): string
    {
        return self::$sessionNewsletter;
    }

    /**
     * Retrieve paypal session model object
     */
    final protected function getPaypalSession(): Mage_Paypal_Model_Session
    {
        /** @var Mage_Paypal_Model_Session $session */
        $session = Mage::getSingleton(self::$sessionPaypal);
        return $session;
    }

    final protected function getPaypalSessionStorage(): string
    {
        return self::$sessionPaypal;
    }

    /**
     * Retrieve reportd session model object
     */
    final protected function getReportsSession(): Mage_Reports_Model_Session
    {
        /** @var Mage_Reports_Model_Session $session */
        $session = Mage::getSingleton(self::$sessionReports);
        return $session;
    }

    final protected function getReportsSessionStorage(): string
    {
        return self::$sessionReports;
    }

    /**
     * Retrieve review session model object
     */
    final protected function getReviewSession(): Mage_Review_Model_Session
    {
        /** @var Mage_Review_Model_Session $session */
        $session = Mage::getSingleton(self::$sessionReview);
        return $session;
    }

    final protected function getReviewSessionStorage(): string
    {
        return self::$sessionReview;
    }

    /**
     * Retrieve review session model object
     */
    final protected function getRssSession(): Mage_Rss_Model_Session
    {
        /** @var Mage_Rss_Model_Session $session */
        $session = Mage::getSingleton(self::$sessionRss);
        return $session;
    }

    final protected function getRssSessionStorage(): string
    {
        return self::$sessionRss;
    }

    /**
     * Retrieve tag session model object
     */
    final protected function getTagSession(): Mage_Tag_Model_Session
    {
        /** @var Mage_Tag_Model_Session $session */
        $session = Mage::getSingleton(self::$sessionTag);
        return $session;
    }

    final protected function getTagSessionStorage(): string
    {
        return self::$sessionTag;
    }

    /**
     * Retrieve wishlist session model object
     */
    final protected function getWishlistSession(): Mage_Wishlist_Model_Session
    {
        /** @var Mage_Wishlist_Model_Session $session */
        $session = Mage::getSingleton(self::$sessionWishlist);
        return $session;
    }

    final protected function getWishlistSessionStorage(): string
    {
        return self::$sessionWishlist;
    }
}
