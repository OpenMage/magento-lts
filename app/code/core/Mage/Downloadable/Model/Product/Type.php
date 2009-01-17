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
 * @copyright   Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
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
     * @return array
     */
    public function getLinks()
    {
        $product = $this->getProduct();
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
     * @return boolean
     */
    public function hasLinks()
    {
        return count($this->getLinks()) > 0;
    }

    /**
     * Check if product has options
     *
     * @return boolean
     */
    public function hasOptions()
    {
        return true;
        return $this->getProduct()->getLinksPurchasedSeparately() || parent::hasOptions();
    }

    /**
     * Check if product cannot be purchased with no links selected
     *
     * @return boolean
     */
    public function getLinkSelectionRequired()
    {
        return $this->getProduct()->getLinksPurchasedSeparately();
    }

    /**
     * Get downloadable product samples
     *
     * @return Mage_Downloadable_Model_Mysql4_Sample_Collection
     */
    public function getSamples()
    {
        $product = $this->getProduct();
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
     * @return boolean
     */
    public function hasSamples()
    {
        return count($this->getSamples()) > 0;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Downloadable_Model_Product_Type
     */
    public function save()
    {
        parent::save();

        $product = $this->getProduct();
        /* @var Mage_Catalog_Model_Product $product */

        if ($data = $product->getDownloadableData()) {
            if (isset($data['sample'])) {
                $_deleteItems = array();
                foreach ($data['sample'] as $sampleItem) {
//                    Zend_Debug::dump($sampleItem);die();
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
                            $files = Zend_Json::decode($sampleItem['file']);
                            unset($sampleItem['file']);
                        }

                        $sampleModel->setData($sampleItem)
                            ->setSampleType($sampleItem['type'])
                            ->setProductId($product->getId())
                            ->setStoreId($product->getStoreId());
                        $fileStatusNew = false;
                        if (isset($files[0]) && $sampleModel->getSampleType() == Mage_Downloadable_Helper_Download::LINK_TYPE_FILE) {
                            $sampleModel->setSampleFile($files[0]['file']);
                            if ($files[0]['status'] == 'new') {
                                $fileStatusNew = true;
                            }
                        }

                        $sampleModel->save();
                        if ($sampleModel->getSampleType() == Mage_Downloadable_Helper_Download::LINK_TYPE_FILE && $fileStatusNew) {
                            try {
                                Mage::helper('downloadable/file')->moveFileFromTmp(
                                    Mage_Downloadable_Model_Sample::getBaseTmpPath(),
                                    Mage_Downloadable_Model_Sample::getBasePath(),
                                    $files[0]['file']
                                );
                            } catch (Exception $e) {
                                Mage::throwException(Mage::helper('downloadable')->__('An error occurred while saving the file(s).'));
                            }
                        }
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
                            $files = Zend_Json::decode($linkItem['file']);
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
                            ->setWebsiteId($product->getStore()->getWebsiteId());
                        if ($linkModel->getIsUnlimited()) {
                            $linkModel->setNumberOfDownloads(0);
                        }
                        $fileStatusNew = false;
                        if (isset($files[0]) && $linkModel->getLinkType() == Mage_Downloadable_Helper_Download::LINK_TYPE_FILE) {
                            $linkModel->setLinkFile($files[0]['file']);
                            if ($files[0]['status'] == 'new') {
                                $fileStatusNew = true;
                            }
                        }
                        $sampleFileNew = false;
                        if ($sample && isset($sample['type'])) {
                            if ($sample['type'] == 'url' && $sample['url'] != '') {
                                $linkModel->setSampleUrl($sample['url'])
                                    ->setSampleType($sample['type']);
                            }
                            $sampleFile = Zend_Json::decode($sample['file']);
                            if ($sample['type'] == Mage_Downloadable_Helper_Download::LINK_TYPE_FILE && isset($sampleFile[0])) {
                                $linkModel->setSampleFile($sampleFile[0]['file'])
                                    ->setSampleType($sample['type']);
                                if ($sampleFile[0]['status'] == 'new') {
                                    $sampleFileNew = true;
                                }
                            }
                        }
                        $linkModel->save();
                        if ($linkModel->getLinkType() == Mage_Downloadable_Helper_Download::LINK_TYPE_FILE && $fileStatusNew) {
                            try {
                                Mage::helper('downloadable/file')->moveFileFromTmp(
                                    Mage_Downloadable_Model_Link::getBaseTmpPath(),
                                    Mage_Downloadable_Model_Link::getBasePath(),
                                    $files[0]['file']
                                );
                            } catch (Exception $e) {
                                Mage::throwException(Mage::helper('downloadable')->__('An error occurred while saving the file(s).'));
                            }
                        }
                        if ($linkModel->getSampleType() == Mage_Downloadable_Helper_Download::LINK_TYPE_FILE && $sampleFileNew) {
                            try {
                                Mage::helper('downloadable/file')->moveFileFromTmp(
                                    Mage_Downloadable_Model_Link::getBaseSampleTmpPath(),
                                    Mage_Downloadable_Model_Link::getBaseSamplePath(),
                                    $sampleFile[0]['file']
                                );
                            } catch (Exception $e) {
                                Mage::throwException(Mage::helper('downloadable')->__('An error occurred while saving the file(s).'));
                            }
                        }
                    }
                }
                if ($_deleteItems) {
                    Mage::getResourceModel('downloadable/link')->deleteItems($_deleteItems);
                }
            }
        }

        return $this;
    }

    /**
     * Enter description here...
     *
     * @param Varien_Object $buyRequest
     * @return array|string
     */
    public function prepareForCart(Varien_Object $buyRequest)
    {
        $result = parent::prepareForCart($buyRequest);

        if (is_string($result)) {
            return $result;
        }
        $preparedLinks = array();
        if ($this->getProduct()->getLinksPurchasedSeparately()) {
            if ($links = $buyRequest->getLinks()) {
                foreach ($this->getLinks() as $link) {
                    if (in_array($link->getId(), $links)) {
                        $preparedLinks[] = $link->getId();
                    }
                }
            }
        } else {
            foreach ($this->getLinks() as $link) {
                $preparedLinks[] = $link->getId();
            }
        }
        if ($preparedLinks) {
            $this->getProduct()->addCustomOption('downloadable_link_ids', implode(',', $preparedLinks));
            return $result;
        }
        if ($this->getLinkSelectionRequired()) {
            return Mage::helper('downloadable')->__('Please specify product link(s).');
        }
        return $result;
    }

    public function getOrderOptions()
    {
        $options = parent::getOrderOptions();
        if ($linkIds = $this->getProduct()->getCustomOption('downloadable_link_ids')) {
            $linkOptions = array();
            $links = $this->getLinks();
            foreach (explode(',', $linkIds->getValue()) as $linkId) {
                if (isset($links[$linkId])) {
                    $linkOptions[] = $linkId;
                }
            }
            $options = array_merge($options, array('links' => $linkOptions));
        }
        return $options;
    }

}
