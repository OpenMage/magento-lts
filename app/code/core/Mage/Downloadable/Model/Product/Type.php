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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Downloadable product type model
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Downloadable_Model_Product_Type extends Mage_Catalog_Model_Product_Type_Virtual
{
    const TYPE_DOWNLOADABLE = 'downloadable';

    /**
     * Get downloadable product links
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getLinks($product = null)
    {
        $product = $this->getProduct($product);
        /* @var Mage_Catalog_Model_Product $product */
        if (is_null($product->getDownloadableLinks())) {
            $_linkCollection = Mage::getModel('downloadable/link')->getCollection()
                ->addProductToFilter($product->getId())
                ->addTitleToResult($product->getStoreId())
                ->addPriceToResult($product->getStore()->getWebsiteId());
            $linksCollectionById = array();
            foreach ($_linkCollection as $link) {
                /* @var Mage_Downloadable_Model_Link $link */

                $link->setProduct($product);
                $linksCollectionById[$link->getId()] = $link;
            }
            $product->setDownloadableLinks($linksCollectionById);
        }
        return $product->getDownloadableLinks();
    }

    /**
     * Check if product has links
     *
     * @param Mage_Catalog_Model_Product $product
     * @return boolean
     */
    public function hasLinks($product = null)
    {
        if ($this->getProduct($product)->hasData('links_exist')) {
            return $this->getProduct($product)->getData('links_exist');
        }
        return count($this->getLinks($product)) > 0;
    }

    /**
     * Check if product has options
     *
     * @param Mage_Catalog_Model_Product $product
     * @return boolean
     */
    public function hasOptions($product = null)
    {
        //return true;
        return $this->getProduct($product)->getLinksPurchasedSeparately()
            || parent::hasOptions($product);
    }

    /**
     * Check if product has required options
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function hasRequiredOptions($product = null)
    {
        if (parent::hasRequiredOptions($product) || $this->getProduct($product)->getLinksPurchasedSeparately()) {
            return true;
        }
        return false;
    }

    /**
     * Check if product cannot be purchased with no links selected
     *
     * @param Mage_Catalog_Model_Product $product
     * @return boolean
     */
    public function getLinkSelectionRequired($product = null)
    {
        return $this->getProduct($product)->getLinksPurchasedSeparately();
    }

    /**
     * Get downloadable product samples
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Downloadable_Model_Mysql4_Sample_Collection
     */
    public function getSamples($product = null)
    {
        $product = $this->getProduct($product);
        /* @var Mage_Catalog_Model_Product $product */
        if (is_null($product->getDownloadableSamples())) {
            $_sampleCollection = Mage::getModel('downloadable/sample')->getCollection()
                ->addProductToFilter($product->getId())
                ->addTitleToResult($product->getStoreId());
            $product->setDownloadableSamples($_sampleCollection);
        }

        return $product->getDownloadableSamples();
    }

    /**
     * Check if product has samples
     *
     * @param Mage_Catalog_Model_Product $product
     * @return boolean
     */
    public function hasSamples($product = null)
    {
        return count($this->getSamples($product)) > 0;
    }

    /**
     * Save Product downloadable information (links and samples)
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Downloadable_Model_Product_Type
     */
    public function save($product = null)
    {
        parent::save($product);

        $product = $this->getProduct($product);
        /* @var Mage_Catalog_Model_Product $product */

        if ($data = $product->getDownloadableData()) {
            if (isset($data['sample'])) {
                $_deleteItems = array();
                foreach ($data['sample'] as $sampleItem) {
                    if ($sampleItem['is_delete'] == '1') {
                        if ($sampleItem['sample_id']) {
                            $_deleteItems[] = $sampleItem['sample_id'];
                        }
                    } else {
                        unset($sampleItem['is_delete']);
                        if (!$sampleItem['sample_id']) {
                            unset($sampleItem['sample_id']);
                        }
                        $sampleModel = Mage::getModel('downloadable/sample');
                        $files = array();
                        if (isset($sampleItem['file'])) {
                            $files = Mage::helper('core')->jsonDecode($sampleItem['file']);
                            unset($sampleItem['file']);
                        }

                        $sampleModel->setData($sampleItem)
                            ->setSampleType($sampleItem['type'])
                            ->setProductId($product->getId())
                            ->setStoreId($product->getStoreId());

                        if ($sampleModel->getSampleType() == Mage_Downloadable_Helper_Download::LINK_TYPE_FILE) {
                            $sampleFileName = Mage::helper('downloadable/file')->moveFileFromTmp(
                                Mage_Downloadable_Model_Sample::getBaseTmpPath(),
                                Mage_Downloadable_Model_Sample::getBasePath(),
                                $files
                            );
                            $sampleModel->setSampleFile($sampleFileName);
                        }
                        $sampleModel->save();
                    }
                }
                if ($_deleteItems) {
                    Mage::getResourceModel('downloadable/sample')->deleteItems($_deleteItems);
                }
            }
            if (isset($data['link'])) {
                $_deleteItems = array();
                foreach ($data['link'] as $linkItem) {
                    if ($linkItem['is_delete'] == '1') {
                        if ($linkItem['link_id']) {
                            $_deleteItems[] = $linkItem['link_id'];
                        }
                    } else {
                        unset($linkItem['is_delete']);
                        if (!$linkItem['link_id']) {
                            unset($linkItem['link_id']);
                        }
                        $files = array();
                        if (isset($linkItem['file'])) {
                            $files = Mage::helper('core')->jsonDecode($linkItem['file']);
                            unset($linkItem['file']);
                        }
                        $sample = array();
                        if (isset($linkItem['sample'])) {
                            $sample = $linkItem['sample'];
                            unset($linkItem['sample']);
                        }
                        $linkModel = Mage::getModel('downloadable/link')
                            ->setData($linkItem)
                            ->setLinkType($linkItem['type'])
                            ->setProductId($product->getId())
                            ->setStoreId($product->getStoreId())
                            ->setWebsiteId($product->getStore()->getWebsiteId())
                            ->setProductWebsiteIds($product->getWebsiteIds());
                        if (null === $linkModel->getPrice()) {
                            $linkModel->setPrice(0);
                        }
                        if ($linkModel->getIsUnlimited()) {
                            $linkModel->setNumberOfDownloads(0);
                        }
                        $sampleFile = array();
                        if ($sample && isset($sample['type'])) {
                            if ($sample['type'] == 'url' && $sample['url'] != '') {
                                $linkModel->setSampleUrl($sample['url']);
                            }
                            $linkModel->setSampleType($sample['type']);
                            $sampleFile = Mage::helper('core')->jsonDecode($sample['file']);
                        }
                        if ($linkModel->getLinkType() == Mage_Downloadable_Helper_Download::LINK_TYPE_FILE) {
                            $linkFileName = Mage::helper('downloadable/file')->moveFileFromTmp(
                                Mage_Downloadable_Model_Link::getBaseTmpPath(),
                                Mage_Downloadable_Model_Link::getBasePath(),
                                $files
                            );
                            $linkModel->setLinkFile($linkFileName);
                        }
                        if ($linkModel->getSampleType() == Mage_Downloadable_Helper_Download::LINK_TYPE_FILE) {
                            $linkSampleFileName = Mage::helper('downloadable/file')->moveFileFromTmp(
                                Mage_Downloadable_Model_Link::getBaseSampleTmpPath(),
                                Mage_Downloadable_Model_Link::getBaseSamplePath(),
                                $sampleFile
                            );
                            $linkModel->setSampleFile($linkSampleFileName);
                        }
                        $linkModel->save();
                    }
                }
                if ($_deleteItems) {
                    Mage::getResourceModel('downloadable/link')->deleteItems($_deleteItems);
                }
                if ($this->getProduct($product)->getLinksPurchasedSeparately()) {
                    $this->getProduct($product)->setIsCustomOptionChanged();
                }
            }
        }

        return $this;
    }

    /**
     * Prepare Product object before adding to Shopping Cart
     *
     * @param Varien_Object $buyRequest
     * @param Mage_Catalog_Model_Product $product
     * @return array|string
     */
    public function prepareForCart(Varien_Object $buyRequest, $product = null)
    {
        $result = parent::prepareForCart($buyRequest, $product);

        if (is_string($result)) {
            return $result;
        }
        // if adding product from admin area we add all links to product
        if ($this->getProduct($product)->getSkipCheckRequiredOption()) {
            $this->getProduct($product)->setLinksPurchasedSeparately(false);
        }
        $preparedLinks = array();
        if ($this->getProduct($product)->getLinksPurchasedSeparately()) {
            if ($links = $buyRequest->getLinks()) {
                foreach ($this->getLinks($product) as $link) {
                    if (in_array($link->getId(), $links)) {
                        $preparedLinks[] = $link->getId();
                    }
                }
            }
        } else {
            foreach ($this->getLinks($product) as $link) {
                $preparedLinks[] = $link->getId();
            }
        }
        if ($preparedLinks) {
            $this->getProduct($product)->addCustomOption('downloadable_link_ids', implode(',', $preparedLinks));
            return $result;
        }
        if ($this->getLinkSelectionRequired($product)) {
            return Mage::helper('downloadable')->__('Please specify product link(s).');
        }
        return $result;
    }

    /**
     * Prepare additional options/information for order item which will be
     * created from this product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getOrderOptions($product = null)
    {
        $options = parent::getOrderOptions($product);
        if ($linkIds = $this->getProduct($product)->getCustomOption('downloadable_link_ids')) {
            $linkOptions = array();
            $links = $this->getLinks($product);
            foreach (explode(',', $linkIds->getValue()) as $linkId) {
                if (isset($links[$linkId])) {
                    $linkOptions[] = $linkId;
                }
            }
            $options = array_merge($options, array('links' => $linkOptions));
        }
        $options = array_merge($options, array(
            'is_downloadable' => true,
            'real_product_type' => self::TYPE_DOWNLOADABLE
        ));
        return $options;
    }



    /**
     * Setting flag if dowenloadable product can be or not in complex product
     * based on link can be purchased separately or not
     *
     * @param Mage_Catalog_Model_Product $product
     */
    public function beforeSave($product = null)
    {
        parent::beforeSave($product);
        if ($this->getLinkSelectionRequired($product)) {
            $this->getProduct($product)->setTypeHasRequiredOptions(true);
        } else {
            $this->getProduct($product)->setTypeHasRequiredOptions(false);
        }

        // Update links_exist attribute value
        $linksExist = false;
        if ($data = $product->getDownloadableData()) {
            if (isset($data['link'])) {
                foreach ($data['link'] as $linkItem) {
                    if (!isset($linkItem['is_delete']) || !$linkItem['is_delete']) {
                        $linksExist = true;
                        break;
                    }
                }
            }
        }

        /*
         * After "Downloadable Information" tab was made non-ajax we should
         * set this flag "true" to force saving of 'required_options' attribute
         */
        $this->getProduct($product)->setCanSaveCustomOptions(true);
        $this->getProduct($product)->setTypeHasOptions($linksExist);
        $this->getProduct($product)->setLinksExist($linksExist);
    }

    /**
     * Retrieve additional searchable data from type instance
     * Using based on product id and store_id data
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getSearchableData($product = null)
    {
        $searchData = parent::getSearchableData($product);
        $product = $this->getProduct($product);

        $linkSearchData = Mage::getSingleton('downloadable/link')
            ->getSearchableData($product->getId(), $product->getStoreId());
        if ($linkSearchData) {
            $searchData = array_merge($searchData, $linkSearchData);
        }

        $sampleSearchData = Mage::getSingleton('downloadable/sample')
            ->getSearchableData($product->getId(), $product->getStoreId());
        if ($sampleSearchData) {
            $searchData = array_merge($searchData, $sampleSearchData);
        }

        return $searchData;
    }

    /**
     * Check is product available for sale
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function isSalable($product = null)
    {
        return $this->hasLinks($product) && parent::isSalable($product);
    }
}
