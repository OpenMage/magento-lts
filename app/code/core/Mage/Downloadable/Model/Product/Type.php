<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Downloadable
 */

/**
 * Downloadable product type model
 *
 * @package    Mage_Downloadable
 */
class Mage_Downloadable_Model_Product_Type extends Mage_Catalog_Model_Product_Type_Virtual
{
    public const TYPE_DOWNLOADABLE = 'downloadable';

    /**
     * Get downloadable product links
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Downloadable_Model_Link[]
     */
    public function getLinks($product = null)
    {
        $product = $this->getProduct($product);
        if (is_null($product->getDownloadableLinks())) {
            $_linkCollection = Mage::getModel('downloadable/link')->getCollection()
                ->addProductToFilter($product->getId())
                ->addTitleToResult($product->getStoreId())
                ->addPriceToResult($product->getStore()->getWebsiteId());
            $linksCollectionById = [];
            foreach ($_linkCollection as $link) {
                /** @var Mage_Downloadable_Model_Link $link */
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
     * @return bool
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
     * @return bool
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
     * @return bool
     */
    public function getLinkSelectionRequired($product = null)
    {
        return $this->getProduct($product)->getLinksPurchasedSeparately();
    }

    /**
     * Get downloadable product samples
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Downloadable_Model_Resource_Sample_Collection
     */
    public function getSamples($product = null)
    {
        $product = $this->getProduct($product);
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
     * @return bool
     */
    public function hasSamples($product = null)
    {
        return count($this->getSamples($product)) > 0;
    }

    /**
     * Save Product downloadable information (links and samples)
     *
     * @param Mage_Catalog_Model_Product $product
     * @return $this
     */
    public function save($product = null)
    {
        parent::save($product);

        $product = $this->getProduct($product);
        if ($data = $product->getDownloadableData()) {
            if (isset($data['sample'])) {
                $_deleteItems = [];
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
                        $files = [];
                        if (isset($sampleItem['file'])) {
                            $files = Mage::helper('core')->jsonDecode($sampleItem['file']);
                            unset($sampleItem['file']);
                        }

                        if (isset($sampleItem['sample_url'])) {
                            $sampleItem['sample_url'] = Mage::helper('core')->escapeUrl($sampleItem['sample_url']);
                        }

                        $sampleModel->setData($sampleItem)
                            ->setSampleType($sampleItem['type'])
                            ->setProductId($product->getId())
                            ->setStoreId($product->getStoreId());

                        if ($sampleModel->getSampleType() == Mage_Downloadable_Helper_Download::LINK_TYPE_FILE) {
                            $sampleFileName = Mage::helper('downloadable/file')->moveFileFromTmp(
                                Mage_Downloadable_Model_Sample::getBaseTmpPath(),
                                Mage_Downloadable_Model_Sample::getBasePath(),
                                $files,
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
                $_deleteItems = [];
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

                        $files = [];
                        if (isset($linkItem['file'])) {
                            $files = Mage::helper('core')->jsonDecode($linkItem['file']);
                            unset($linkItem['file']);
                        }

                        $sample = [];
                        if (isset($linkItem['sample'])) {
                            $sample = $linkItem['sample'];
                            unset($linkItem['sample']);
                        }

                        if (isset($linkItem['link_url'])) {
                            $linkItem['link_url'] = Mage::helper('core')->escapeUrl($linkItem['link_url']);
                        }

                        $linkModel = Mage::getModel('downloadable/link')
                            ->setData($linkItem)
                            ->setLinkType($linkItem['type'])
                            ->setProductId($product->getId())
                            ->setStoreId($product->getStoreId())
                            ->setWebsiteId($product->getStore()->getWebsiteId())
                            ->setProductWebsiteIds($product->getWebsiteIds());
                        if ($linkModel->getPrice() === null) {
                            $linkModel->setPrice(0);
                        }

                        if ($linkModel->getIsUnlimited()) {
                            $linkModel->setNumberOfDownloads(0);
                        }

                        $sampleFile = [];
                        if ($sample && isset($sample['type'])) {
                            if ($sample['type'] == 'url' && $sample['url'] != '') {
                                $linkModel->setSampleUrl(Mage::helper('core')->escapeUrl($sample['url']));
                            }

                            $linkModel->setSampleType($sample['type']);
                            $sampleFile = Mage::helper('core')->jsonDecode($sample['file']);
                        }

                        if ($linkModel->getLinkType() == Mage_Downloadable_Helper_Download::LINK_TYPE_FILE) {
                            $linkFileName = Mage::helper('downloadable/file')->moveFileFromTmp(
                                Mage_Downloadable_Model_Link::getBaseTmpPath(),
                                Mage_Downloadable_Model_Link::getBasePath(),
                                $files,
                            );
                            $linkModel->setLinkFile($linkFileName);
                        }

                        if ($linkModel->getSampleType() == Mage_Downloadable_Helper_Download::LINK_TYPE_FILE) {
                            $linkSampleFileName = Mage::helper('downloadable/file')->moveFileFromTmp(
                                Mage_Downloadable_Model_Link::getBaseSampleTmpPath(),
                                Mage_Downloadable_Model_Link::getBaseSamplePath(),
                                $sampleFile,
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
     * Prepare product and its configuration to be added to some products list.
     * Perform standard preparation process and then prepare options for downloadable links.
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $processMode
     * @return array|string
     */
    protected function _prepareProduct(Varien_Object $buyRequest, $product, $processMode)
    {
        $result = parent::_prepareProduct($buyRequest, $product, $processMode);

        if (is_string($result)) {
            return $result;
        }

        // if adding product from admin area we add all links to product
        $originalLinksPurchasedSeparately = null;
        if ($this->getProduct($product)->getSkipCheckRequiredOption()) {
            $originalLinksPurchasedSeparately = $this->getProduct($product)
                ->getLinksPurchasedSeparately();
            $this->getProduct($product)->setLinksPurchasedSeparately(false);
        }

        $preparedLinks = [];
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

        if ($originalLinksPurchasedSeparately !== null) {
            $this->getProduct($product)
                ->setLinksPurchasedSeparately($originalLinksPurchasedSeparately);
        }

        if ($preparedLinks) {
            $this->getProduct($product)->addCustomOption('downloadable_link_ids', implode(',', $preparedLinks));
            return $result;
        }

        if ($this->getLinkSelectionRequired($product) && $this->_isStrictProcessMode($processMode)) {
            return Mage::helper('downloadable')->__('Please specify product link(s).');
        }

        return $result;
    }

    /**
     * Check if product can be bought
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Downloadable_Model_Product_Type
     * @throws Mage_Core_Exception
     */
    public function checkProductBuyState($product = null)
    {
        parent::checkProductBuyState($product);
        $product = $this->getProduct($product);
        $option = $product->getCustomOption('info_buyRequest');
        if ($option instanceof Mage_Sales_Model_Quote_Item_Option) {
            $buyRequest = new Varien_Object(unserialize($option->getValue(), ['allowed_classes' => false]));
            if (!$buyRequest->hasLinks()) {
                if (!$product->getLinksPurchasedSeparately()) {
                    $allLinksIds = Mage::getModel('downloadable/link')
                        ->getCollection()
                        ->addProductToFilter($product->getId())
                        ->getAllIds();
                    $buyRequest->setLinks($allLinksIds);
                    $product->addCustomOption('info_buyRequest', serialize($buyRequest->getData()));
                } else {
                    Mage::throwException(
                        Mage::helper('downloadable')->__('Please specify product link(s).'),
                    );
                }
            }
        }

        return $this;
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
            $linkOptions = [];
            $links = $this->getLinks($product);
            foreach (explode(',', $linkIds->getValue()) as $linkId) {
                if (isset($links[$linkId])) {
                    $linkOptions[] = $linkId;
                }
            }

            $options = array_merge($options, ['links' => $linkOptions]);
        }

        return array_merge($options, [
            'is_downloadable' => true,
            'real_product_type' => self::TYPE_DOWNLOADABLE,
        ]);
    }

    /**
     * Setting flag if dowenloadable product can be or not in complex product
     * based on link can be purchased separately or not
     *
     * @param Mage_Catalog_Model_Product $product
     * @return $this
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

        $this->getProduct($product)->setTypeHasOptions($linksExist);
        $this->getProduct($product)->setLinksExist($linksExist);
        return $this;
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

    /**
     * Prepare selected options for downloadable product
     *
     * @param  Mage_Catalog_Model_Product $product
     * @param  Varien_Object $buyRequest
     * @return array
     */
    public function processBuyRequest($product, $buyRequest)
    {
        $links = $buyRequest->getLinks();
        $links = (is_array($links)) ? array_filter($links, \intval(...)) : [];

        return ['links' => $links];
    }

    /**
     * Check if downloadable product has links and they can be purchased separately
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function canConfigure($product = null)
    {
        return $this->hasLinks($product) && $this->getProduct($product)->getLinksPurchasedSeparately();
    }
}
