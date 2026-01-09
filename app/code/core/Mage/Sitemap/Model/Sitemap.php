<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sitemap
 */

/**
 * Sitemap model
 *
 * @package    Mage_Sitemap
 *
 * @method Mage_Sitemap_Model_Resource_Sitemap            _getResource()
 * @method Mage_Sitemap_Model_Resource_Sitemap_Collection getCollection()
 * @method Mage_Sitemap_Model_Resource_Sitemap            getResource()
 * @method Mage_Sitemap_Model_Resource_Sitemap_Collection getResourceCollection()
 * @method string                                         getSitemapFilename()
 * @method int                                            getSitemapId()
 * @method string                                         getSitemapPath()
 * @method string                                         getSitemapTime()
 * @method string                                         getSitemapType()
 * @method int                                            getStoreId()
 * @method $this                                          setSitemapFilename(string $value)
 * @method $this                                          setSitemapPath(string $value)
 * @method $this                                          setSitemapTime(string $value)
 * @method $this                                          setSitemapType(string $value)
 * @method $this                                          setStoreId(int $value)
 */
class Mage_Sitemap_Model_Sitemap extends Mage_Core_Model_Abstract
{
    /**
     * Real file path
     *
     * @var null|string
     */
    protected $_filePath;

    /**
     * @inheritDoc
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
         * Check path is allow
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
            $this->_filePath = str_replace('//', '/', Mage::getBaseDir()
                . $this->getSitemapPath());
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
        $io->streamWrite('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');

        $storeId = $this->getStoreId();
        $date    = Mage::getSingleton('core/date')->gmtDate('Y-m-d');
        $baseUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);

        /**
         * Generate categories sitemap
         */
        $changefreq = (string) Mage::getStoreConfig('sitemap/category/changefreq', $storeId);
        $priority   = (string) Mage::getStoreConfig('sitemap/category/priority', $storeId);
        $lastmod    = Mage::getStoreConfigFlag('sitemap/category/lastmod', $storeId) ? $date : '';
        $collection = Mage::getResourceModel('sitemap/catalog_category')->getCollection($storeId);
        $categories = new Varien_Object();
        $categories->setItems($collection);
        Mage::dispatchEvent('sitemap_categories_generating_before', [
            'collection' => $categories,
            'store_id' => $storeId,
        ]);
        foreach ($categories->getItems() as $item) {
            $xml = $this->getSitemapRow($baseUrl . $item->getUrl(), $lastmod, $changefreq, $priority);
            $io->streamWrite($xml);
        }

        unset($collection);

        /**
         * Generate products sitemap
         */
        $changefreq = (string) Mage::getStoreConfig('sitemap/product/changefreq', $storeId);
        $priority   = (string) Mage::getStoreConfig('sitemap/product/priority', $storeId);
        $lastmod    = Mage::getStoreConfigFlag('sitemap/product/lastmod', $storeId) ? $date : '';
        $collection = Mage::getResourceModel('sitemap/catalog_product')->getCollection($storeId);
        $products = new Varien_Object();
        $products->setItems($collection);
        Mage::dispatchEvent('sitemap_products_generating_before', [
            'collection' => $products,
            'store_id' => $storeId,
        ]);
        foreach ($products->getItems() as $item) {
            $xml = $this->getSitemapRow($baseUrl . $item->getUrl(), $lastmod, $changefreq, $priority);
            $io->streamWrite($xml);
        }

        unset($collection);

        /**
         * Generate cms pages sitemap
         */
        $homepage = (string) Mage::getStoreConfig('web/default/cms_home_page', $storeId);
        $changefreq = (string) Mage::getStoreConfig('sitemap/page/changefreq', $storeId);
        $priority   = (string) Mage::getStoreConfig('sitemap/page/priority', $storeId);
        $lastmod    = Mage::getStoreConfigFlag('sitemap/page/lastmod', $storeId) ? $date : '';
        $collection = Mage::getResourceModel('sitemap/cms_page')->getCollection($storeId);
        $pages = new Varien_Object();
        $pages->setItems($collection);
        Mage::dispatchEvent('sitemap_cms_pages_generating_before', [
            'collection' => $pages,
            'store_id' => $storeId,
        ]);
        foreach ($pages->getItems() as $item) {
            $url = $item->getUrl();
            if ($url == $homepage) {
                $url = '';
            }

            $xml = $this->getSitemapRow($baseUrl . $url, $lastmod, $changefreq, $priority);
            $io->streamWrite($xml);
        }

        unset($collection);

        Mage::dispatchEvent('sitemap_urlset_generating_before', [
            'file'      => $io ,
            'base_url'  => $baseUrl ,
            'date'      => $date,
            'store_id'  => $storeId,
        ]);

        $io->streamWrite('</urlset>');
        $io->streamClose();

        $this->setSitemapTime(
            Mage::getSingleton('core/date')->gmtDate(Varien_Db_Adapter_Pdo_Mysql::TIMESTAMP_FORMAT),
        );
        $this->save();

        return $this;
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
