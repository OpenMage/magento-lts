<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sitemap
 */

/**
 * Sitemap module observer
 *
 * @package    Mage_Sitemap
 */
class Mage_Sitemap_Model_Observer
{
    /**
     * Enable/disable configuration
     */
    public const XML_PATH_GENERATION_ENABLED = 'sitemap/generate/enabled';

    /**
     * Cronjob expression configuration
     */
    public const XML_PATH_CRON_EXPR = 'crontab/jobs/generate_sitemaps/schedule/cron_expr';

    /**
     * Error email template configuration
     */
    public const XML_PATH_ERROR_TEMPLATE  = 'sitemap/generate/error_email_template';

    /**
     * Error email identity configuration
     */
    public const XML_PATH_ERROR_IDENTITY  = 'sitemap/generate/error_email_identity';

    /**
     * 'Send error emails to' configuration
     */
    public const XML_PATH_ERROR_RECIPIENT = 'sitemap/generate/error_email';

    /**
     * Generate sitemaps
     *
     * @param  Mage_Cron_Model_Schedule $schedule
     * @return void
     * @throws Mage_Core_Exception
     */
    public function scheduledGenerateSitemaps($schedule)
    {
        $errors = [];

        // check if scheduled generation enabled
        if (!Mage::getStoreConfigFlag(self::XML_PATH_GENERATION_ENABLED)) {
            return;
        }

        $collection = Mage::getModel('sitemap/sitemap')->getCollection();
        /** @var Mage_Sitemap_Model_Resource_Sitemap_Collection $collection */
        foreach ($collection as $sitemap) {
            /** @var Mage_Sitemap_Model_Sitemap $sitemap */

            try {
                $sitemap->generateXml();
            } catch (Throwable $throwable) {
                $errors[] = $throwable->getMessage();
            }
        }

        if ($errors && Mage::getStoreConfig(self::XML_PATH_ERROR_RECIPIENT)) {
            $translate = Mage::getSingleton('core/translate');
            /** @var Mage_Core_Model_Translate $translate */
            $translate->setTranslateInline(false);

            $emailTemplate = Mage::getModel('core/email_template');
            /** @var Mage_Core_Model_Email_Template $emailTemplate */
            $emailTemplate->setDesignConfig(['area' => 'backend'])
                ->sendTransactional(
                    Mage::getStoreConfig(self::XML_PATH_ERROR_TEMPLATE),
                    Mage::getStoreConfig(self::XML_PATH_ERROR_IDENTITY),
                    Mage::getStoreConfig(self::XML_PATH_ERROR_RECIPIENT),
                    null,
                    ['warnings' => implode("\n", $errors)],
                );

            $translate->setTranslateInline(true);
        }
    }
}
