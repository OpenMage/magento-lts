<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sitemap
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Sitemap module observer
 *
 * @category   Mage
 * @package    Mage_Sitemap
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sitemap_Model_Observer
{

    /**
     * Enable/disable configuration
     */
    const XML_PATH_GENERATION_ENABLED = 'sitemap/generate/enabled';

    /**
     * Cronjob expression configuration
     */
    const XML_PATH_CRON_EXPR = 'crontab/jobs/generate_sitemaps/schedule/cron_expr';

    /**
     * Error email template configuration
     */
    const XML_PATH_ERROR_TEMPLATE  = 'sitemap/generate/error_email_template';

    /**
     * Error email identity configuration
     */
    const XML_PATH_ERROR_IDENTITY  = 'sitemap/generate/error_email_identity';

    /**
     * 'Send error emails to' configuration
     */
    const XML_PATH_ERROR_RECIPIENT = 'sitemap/generate/error_email';

    /**
     * Generate sitemaps
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledGenerateSitemaps($schedule)
    {
        $errors = array();

        // check if scheduled generation enabled
        if (!Mage::getStoreConfigFlag(self::XML_PATH_GENERATION_ENABLED)) {
            return;
        }

        $collection = Mage::getModel('sitemap/sitemap')->getCollection();
        /* @var Mage_Sitemap_Model_Resource_Sitemap_Collection $collection */
        foreach ($collection as $sitemap) {
            /* @var Mage_Sitemap_Model_Sitemap $sitemap */

            try {
                $sitemap->generateXml();
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        if ($errors && Mage::getStoreConfig(self::XML_PATH_ERROR_RECIPIENT)) {
            $translate = Mage::getSingleton('core/translate');
            /* @var Mage_Core_Model_Translate $translate */
            $translate->setTranslateInline(false);

            $emailTemplate = Mage::getModel('core/email_template');
            /* @var Mage_Core_Model_Email_Template $emailTemplate */
            $emailTemplate->setDesignConfig(array('area' => 'backend'))
                ->sendTransactional(
                    Mage::getStoreConfig(self::XML_PATH_ERROR_TEMPLATE),
                    Mage::getStoreConfig(self::XML_PATH_ERROR_IDENTITY),
                    Mage::getStoreConfig(self::XML_PATH_ERROR_RECIPIENT),
                    null,
                    array('warnings' => implode("\n", $errors))
                );

            $translate->setTranslateInline(true);
        }
    }
}
