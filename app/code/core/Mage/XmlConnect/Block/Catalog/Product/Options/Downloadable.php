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
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Downloadable product options xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Catalog_Product_Options_Downloadable extends Mage_XmlConnect_Block_Catalog_Product_Options
{
    /**
     * Generate downloadable product options xml
     *
     * @param Mage_Catalog_Model_Product $product
     * @param bool $isObject
     * @return string | Mage_XmlConnect_Model_Simplexml_Element
     */
    public function getProductOptionsXml(Mage_Catalog_Model_Product $product, $isObject = false)
    {
        /** set current product object */
        $this->setProduct($product);

        /** @var $xmlModel Mage_XmlConnect_Model_Simplexml_Element */
        $xmlModel = $this->getProductCustomOptionsXmlObject($product);

        /** @var $linksBlock Mage_Downloadable_Block_Catalog_Product_Links */
        $linksBlock = $this->getLayout()->addBlock('downloadable/catalog_product_links', 'product_links');

        if (!$product->isSalable() || !$linksBlock->hasLinks()) {
            return $isObject ? $xmlModel : $xmlModel->asNiceXml();
        }

        /** @var $optionsXmlObj Mage_XmlConnect_Model_Simplexml_Element */
        $optionsXmlObj = $xmlModel->options;

        /** @var $samplesBlock Mage_Downloadable_Block_Catalog_Product_Samples */
        $samplesBlock = $this->getLayout()->addBlock('downloadable/catalog_product_samples', 'product_samples');

        if ($samplesBlock->hasSamples()) {
            $samplesXmlObj = $optionsXmlObj->addCustomChild('samples', null, array(
                'label' => $samplesBlock->getSamplesTitle()
            ));
            $samples = $samplesBlock->getSamples();
            foreach ($samples as $sample) {
                $samplesXmlObj->addCustomChild('item', null, array(
                    'label' => $sample->getTitle(),
                    'url' => $samplesBlock->getSampleUrl($sample)
                ));
            }
        }

        $linksOptions = array('label' => $linksBlock->getLinksTitle());
        $selectionRequired = $linksBlock->getLinkSelectionRequired();
        if ($selectionRequired) {
            $linksOptions += array('code' => 'links[]');
        }
        $linksXmlObj = $optionsXmlObj->addCustomChild('links', null, $linksOptions);
        $links = $linksBlock->getLinks();
        foreach ($links as $link) {
            $linkOptions = array('label' => $link->getTitle());

            if ($selectionRequired) {
                $linkOptions += array('value' => $link->getId());
                $price = strip_tags($linksBlock->getFormattedLinkPrice($link));
                if ($price) {
                    $linkOptions += array('formatted_price' => $price);
                }
            }

            if ($product->hasPreconfiguredValues()) {
                $optionData = $product->getPreconfiguredValues()->getData('links');
            }
            $isSelected = isset($optionData) ? in_array($link->getId(), $optionData) : false;

            if ($linksBlock->getLinkCheckedValue($link) || $isSelected) {
                $linkOptions += array('selected' => '1');
            }

            if ($link->getSampleFile() || $link->getSampleUrl()) {
                $linkOptions += array('sample_url' => $linksBlock->getLinkSamlpeUrl($link));
            }

            $linksXmlObj->addCustomChild('item', null, $linkOptions);
        }

        return $isObject ? $xmlModel : $xmlModel->asNiceXml();
    }
}
