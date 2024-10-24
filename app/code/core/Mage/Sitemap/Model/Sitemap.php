<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sitemap
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2016-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sitemap model
 *
 * @category   Mage
 * @package    Mage_Sitemap
 *
 * @method Mage_Sitemap_Model_Resource_Sitemap _getResource()
 * @method Mage_Sitemap_Model_Resource_Sitemap getResource()
 * @method Mage_Sitemap_Model_Resource_Sitemap_Collection getCollection()
 *
 * @method int getSitemapId()
 * @method string getSitemapType()
 * @method $this setSitemapType(string $value)
 * @method string getSitemapFilename()
 * @method $this setSitemapFilename(string $value)
 * @method string getSitemapPath()
 * @method $this setSitemapPath(string $value)
 * @method string getSitemapTime()
 * @method $this setSitemapTime(string $value)
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 */
class Mage_Sitemap_Model_Sitemap extends Mage_Core_Model_Abstract
{
    /**
     * Real file path
     *
     * @var string|null
     */
    protected $_filePath;

    /**
     * Init model
     */
    protected function _construct()
    {
        $this->_init('sitemap/sitemap');
    }

    /**
     * @inheritDoc
     * @throws Mage_Core_Exception
     */
    protected function _beforeSave()
    {
        $io = new Varien_Io_File();
        $realPath = $io->getCleanPath(Mage::getBaseDir() . '/' . $this->getSitemapPath());

        /**
         * Check path is allowed
         */
        if (!$io->allowedPath($realPath, Mage::getBaseDir())) {
            Mage::throwException(Mage::helper('sitemap')->__('Please define correct path'));
        }
        /**
         * Check exists and writeable path
         */
        if (!$io->fileExists($realPath, false)) {
            Mage::throwException(Mage::helper('sitemap')->__('Please create the specified folder "%s" before saving the sitemap.', Mage::helper('core')->escapeHtml($this->getSitemapPath())));
        }

        if (!$io->isWriteable($realPath)) {
            Mage::throwException(Mage::helper('sitemap')->__('Please make sure that "%s" is writable by web-server.', $this->getSitemapPath()));
        }
        /**
         * Check allow filename
         */
        if (!preg_match('#^[a-zA-Z0-9_\.]+$#', $this->getSitemapFilename())) {
            Mage::throwException(Mage::helper('sitemap')->__('Please use only letters (a-z or A-Z), numbers (0-9) or underscore (_) in the filename. No spaces or other characters are allowed.'));
        }
        if (!preg_match('#\.xml$#', $this->getSitemapFilename())) {
            $this->setSitemapFilename($this->getSitemapFilename() . '.xml');
        }

        $this->setSitemapPath(rtrim(str_replace(str_replace('\\', '/', Mage::getBaseDir()), '', $realPath), '/') . '/');

        return parent::_beforeSave();
    }

    /**
     * Return real file path
     *
     * @return string
     */
    protected function getPath()
    {
        if (is_null($this->_filePath)) {
            $this->_filePath = str_replace('//', '/', Mage::getBaseDir() .
                $this->getSitemapPath());
        }
        return $this->_filePath;
    }

    /**
     * Return full file name with path
     *
     * @return string
     */
    public function getPreparedFilename()
    {
        return $this->getPath() . $this->getSitemapFilename();
    }

    /**
     * Generate XML file
     *
     * @return $this
     * @throws Throwable
     */
    public function generateXml()
    {
        $io = new Varien_Io_File();
        $io->setAllowCreateFolders(true);
        $io->open(['path' => $this->getPath()]);

        if ($io->fileExists($this->getSitemapFilename()) && !$io->isWriteable($this->getSitemapFilename())) {
            Mage::throwException(Mage::helper('sitemap')->__('File "%s" cannot be saved. Please, make sure the directory "%s" is writeable by web server.', $this->getSitemapFilename(), $this->getPath()));
        }

        $io->streamOpen($this->getSitemapFilename());

        $io->streamWrite('<?xml version="1.0" encoding="UTF-8"?>' . "\n");
        $io->streamWrite('<urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9">');

        $config = [
            'storeId' => $storeId = $this->getStoreId(),
            'date'    => $date    = Mage::getSingleton('core/date')->gmtDate('Y-m-d'),
            'baseUrl' => $baseUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK),
        ];

        $this->generateXmlCategories($io, $config);
        $this->generateXmlProducts($io, $config);
        $this->generateXmlCmsPages($io, $config);

        Mage::dispatchEvent('sitemap_urlset_generating_before', [
            'file'      => $io ,
            'base_url'  => $baseUrl ,
            'date'      => $date,
            'store_id'  => $storeId
        ]);

        $io->streamWrite('</urlset>');
        $io->streamClose();

        $this->setSitemapTime(
            Mage::getSingleton('core/date')->gmtDate(Varien_Db_Adapter_Pdo_Mysql::TIMESTAMP_FORMAT)
        );
        $this->save();

        return $this;
    }

    /**
     * Generate categories sitemap
     */
    protected function generateXmlCategories(Varien_Io_File &$io, array $config): void
    {
        if (!Mage::helper('sitemap')->isCategoryEnabled($config['storeId'])) {
            return;
        }

        $collection = Mage::getResourceModel('sitemap/catalog_category')->getCollection($config['storeId']);
        $categories = new Varien_Object();
        $categories->setItems($collection);
        Mage::dispatchEvent('sitemap_categories_generating_before', [
            'collection' => $categories,
            'store_id' => $config['storeId']
        ]);

        list($changeFreq, $priority, $lastMod) = $this->getSitemapConfig('category', $config);
        foreach ($categories->getItems() as $item) {
            $xml = $this->getSitemapRow($config['baseUrl'] . $item->getUrl(), $lastMod, $changeFreq, $priority);
            $io->streamWrite($xml);
        }
        unset($collection);
    }

    /**
     * Generate products sitemap
     */
    protected function generateXmlProducts(Varien_Io_File &$io, array $config): void
    {
        if (!Mage::helper('sitemap')->isProductEnabled($config['storeId'])) {
            return;
        }

        $collection = Mage::getResourceModel('sitemap/catalog_product')->getCollection($config['storeId']);
        $products = new Varien_Object();
        $products->setItems($collection);
        Mage::dispatchEvent('sitemap_products_generating_before', [
            'collection' => $products,
            'store_id' => $config['storeId']
        ]);

        list($changeFreq, $priority, $lastMod) = $this->getSitemapConfig('product', $config);
        foreach ($products->getItems() as $item) {
            $xml = $this->getSitemapRow($config['baseUrl'] . $item->getUrl(), $lastMod, $changeFreq, $priority);
            $io->streamWrite($xml);
        }
        unset($collection);
    }

    /**
     * Generate cms pages sitemap
     */
    protected function generateXmlCmsPages(Varien_Io_File &$io, array $config): void
    {
        if (!Mage::helper('sitemap')->isCmsPageEnabled($config['storeId'])) {
            return;
        }

        $collection = Mage::getResourceModel('sitemap/cms_page')->getCollection($config['storeId']);
        $pages = new Varien_Object();
        $pages->setItems($collection);
        Mage::dispatchEvent('sitemap_cms_pages_generating_before', [
            'collection' => $pages,
            'store_id' => $config['storeId']
        ]);

        list($changeFreq, $priority, $lastMod) = $this->getSitemapConfig('page', $config);
        $homepage = (string)Mage::getStoreConfig(Mage_Cms_Helper_Page::XML_PATH_HOME_PAGE, $config['storeId']);
        foreach ($pages->getItems() as $item) {
            $url = $item->getUrl();
            if ($url == $homepage) {
                $url = '';
            }

            $xml = $this->getSitemapRow($config['baseUrl'] . $url, $lastMod, $changeFreq, $priority);
            $io->streamWrite($xml);
        }
        unset($collection);
    }

    /**
     * @param 'category'|'product'|'page' $type
     */
    public function getSitemapConfig(string $type, array $config): array
    {
        $storeId = $config['storeId'] ?? null;
        $data = $config['date'] ?? '';

        return [
            (string)Mage::getStoreConfig("sitemap/$type/changefreq", $storeId),
            (string)Mage::getStoreConfig("sitemap/$type/priority", $storeId),
            Mage::getStoreConfigFlag("sitemap/$type/lastmod", $storeId) ? $data : ''
        ];
    }

    /**
     * Get sitemap row
     *
     * @param null|string $lastmod
     * @param null|string $changefreq
     * @param null|string $priority
     */
    protected function getSitemapRow(string $url, $lastmod = null, $changefreq = null, $priority = null): string
    {
        $row = '<loc>' . htmlspecialchars($url) . '</loc>';
        if ($lastmod) {
            $row .= '<lastmod>' . $lastmod . '</lastmod>';
        }
        if ($changefreq) {
            $row .= '<changefreq>' . $changefreq . '</changefreq>';
        }
        if ($priority) {
            $row .= sprintf('<priority>%.1f</priority>', $priority);
        }

        return '<url>' . $row . '</url>';
    }
}
