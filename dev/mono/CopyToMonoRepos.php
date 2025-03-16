<?php

namespace OpenMage\Dev\Mono;

use Exception;
use Symfony\Component\Filesystem\Filesystem;

defined('DS') || define('DS', DIRECTORY_SEPARATOR);
defined('PS') || define('PS', PATH_SEPARATOR);

class CopyToMonoRepos
{
    public const MODMAN_FILE = 'modman';
    public const TYPE_SOURCE = 'source';
    public const TYPE_TARGET = 'target';

    private Filesystem $filesystem;
    private string $modmanFile;
    private ?string $copyTarget;

    public function __construct(string $pathToModmanFile, ?string $copyTarget)
    {
        $this->copyTarget = $copyTarget;
        $this->filesystem = new Filesystem();
        $this->modmanFile = $pathToModmanFile;
    }

    public static function process(): void
    {
        foreach (self::getModules() as $module) {
            echo $module . PHP_EOL;
            $modman = new CopyToMonoRepos(
                sprintf('.localdev/%s', $module),
                sprintf('.localdev/%s/src', $module),
            );
            $modman->copyMappedFiles();
        }
    }

    public function copyMappedFiles()
    {
        $targets = $this->getModmanMapping(self::TYPE_TARGET);
        foreach ($targets as $target) {
            if ($this->filesystem->exists($target)) {
                if (is_dir($target)) {
                    $this->filesystem->mkdir($this->copyTarget);
                    $this->filesystem->mirror($target, $this->copyTarget . DS . $target, null, ['override' => true]);
                } else {
                    $this->filesystem->copy($target, $this->copyTarget . DS . $target, true);
                }
            }
        }
    }

    /**
     * @param self::TYPE_*|null $type
     */
    public function getModmanMapping(?string $type = null): array
    {
        $mapped  = [];

        try {
            $content = $this->getModmanFileContent();
            $parts = preg_split('/\s+/', $content);


            foreach ($parts as $index => $path) {
                if (!$path) {
                    continue;
                }
                if ($index % 2 == 0) {
                    $mapped[self::TYPE_SOURCE][] = $path;
                } else {
                    $mapped[self::TYPE_TARGET][] = $path;
                }
            }

            if (!is_null($type)) {
                return $mapped[$type];
            }
        } catch (Exception $exception) {
            echo $exception->getMessage() . PHP_EOL;
        }

        return $mapped;
    }

    public function getModmanFilePath(): string
    {
        return getcwd() . DS . $this->modmanFile . DS . self::MODMAN_FILE;
    }

    /**
     * @throws Exception
     */
    public function getModmanFileContent(): string
    {
        $file = $this->getModmanFilePath();
        if ($this->filesystem->exists($file)) {
            $content = file_get_contents($file);
            if (!$content) {
                return '';
            }
            return $content;
        }
        throw new Exception(sprintf('File %s not found.', $file));
    }

    public static function getModules(): array
    {
        $modules = [];
        $modules['Mage_Admin'] = 'module-admin';
        $modules['Mage_AdminNotification'] = 'module-admin-notification';
        $modules['Mage_Adminhtml'] = 'module-adminhtml';
        $modules['Mage_Api'] = 'module-api';
        $modules['Mage_Api2'] = 'module-api2';
        $modules['Mage_Authorizenet'] = 'module-authorizenet';
        $modules['Mage_Bundle'] = 'module-bundle';
        $modules['Mage_Captcha'] = 'module-captcha';
        $modules['Mage_Catalog'] = 'module-catalog';
        $modules['Mage_CatalogIndex'] = 'module-catalog-index';
        $modules['Mage_CatalogInventory'] = 'module-catalog-inventory';
        $modules['Mage_CatalogRule'] = 'module-catalog-rule';
        $modules['Mage_CatalogSearch'] = 'module-catalog-search';
        $modules['Mage_Centinel'] = 'module-centinel';
        $modules['Mage_Checkout'] = 'module-checkout';
        $modules['Mage_Cms'] = 'module-cms';
        $modules['Mage_ConfigurableWwatches'] = 'module-configurable-swatches';
        $modules['Mage_Contacts'] = 'module-contacts';
        $modules['Mage_Core'] = 'module-core';
        $modules['Mage_Cron'] = 'module-cron';
        $modules['Mage_CurrencySymbol'] = 'module-currency-symbol';
        $modules['Mage_Customer'] = 'module-customer';
        $modules['Mage_Dataflow'] = 'module-dataflow';
        $modules['Mage_Directory'] = 'module-directory';
        $modules['Mage_Downloadable'] = 'module-downloadable';
        $modules['Mage_Eav'] = 'module-eav';
        $modules['Mage_GiftMessage'] = 'module-gift-message';
        $modules['Mage_GoogleAnalytics'] = 'module-google-analytics';
        $modules['Mage_GoogleCheckout'] = 'module-google-checkout';
        $modules['Mage_ImportExport'] = 'module-import-export';
        $modules['Mage_Index'] = 'module-index';
        $modules['Mage_Install'] = 'module-install';
        $modules['Mage_Log'] = 'module-log';
        $modules['Mage_Media'] = 'module-media';
        $modules['Mage_Newsletter'] = 'module-newsletter';
        $modules['Mage_Oauth'] = 'module-oauth';
        $modules['Mage_Page'] = 'module-page';
        $modules['Mage_Paygate'] = 'module-paygate';
        $modules['Mage_Payment'] = 'module-payment';
        $modules['Mage_Paypal'] = 'module-paypal';
        $modules['Mage_PaypalUk'] = 'module-paypal-uk';
        $modules['Mage_Persistent'] = 'module-persistent';
        $modules['Mage_ProductAlert'] = 'module-product-alert';
        $modules['Mage_Rating'] = 'module-rating';
        $modules['Mage_Report'] = 'module-reports';
        $modules['Mage_Review'] = 'module-review';
        $modules['Mage_Rss'] = 'module-rss';
        $modules['Mage_Rule'] = 'module-rule';
        $modules['Mage_Sales'] = 'module-sales';
        $modules['Mage_SalesRule'] = 'module-sales-rule';
        $modules['Mage_Sendfriend'] = 'module-sendfriend';
        $modules['Mage_Shipping'] = 'module-shipping';
        $modules['Mage_Sitemap'] = 'module-sitemap';
        $modules['Mage_Tag'] = 'module-tag';
        $modules['Mage_Tax'] = 'module-tax';
        $modules['Mage_Uploader'] = 'module-uploader';
        $modules['Mage_Usa'] = 'module-usa';
        $modules['Mage_Wee'] = 'module-weee';
        $modules['Mage_Widget'] = 'module-widget';
        $modules['Mage_Wishlist'] = 'module-wishlist';

        return $modules;
    }
}
