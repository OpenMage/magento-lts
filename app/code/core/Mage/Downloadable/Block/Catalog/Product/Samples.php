<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Downloadable
 */

/**
 * Downloadable Product Samples part block
 *
 * @package    Mage_Downloadable
 */
class Mage_Downloadable_Block_Catalog_Product_Samples extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * @return bool
     */
    public function hasSamples()
    {
        /** @var Mage_Downloadable_Model_Product_Type $productType */
        $productType = $this->getProduct()->getTypeInstance(true);
        return $productType->hasSamples($this->getProduct());
    }

    /**
     * Get downloadable product samples
     *
     * @return Mage_Downloadable_Model_Resource_Sample_Collection
     */
    public function getSamples()
    {
        /** @var Mage_Downloadable_Model_Product_Type $productType */
        $productType = $this->getProduct()->getTypeInstance(true);
        return $productType->getSamples($this->getProduct());
    }

    /**
     * @param Mage_Downloadable_Model_Sample $sample
     * @return string
     */
    public function getSampleUrl($sample)
    {
        return $this->getUrl('downloadable/download/sample', ['sample_id' => $sample->getId()]);
    }

    /**
     * Return title of samples section
     *
     * @return string
     */
    public function getSamplesTitle()
    {
        if ($this->getProduct()->getSamplesTitle()) {
            return $this->getProduct()->getSamplesTitle();
        }
        return Mage::getStoreConfig(Mage_Downloadable_Model_Sample::XML_PATH_SAMPLES_TITLE);
    }

    /**
     * Return true if target of link new window
     *
     * @return bool
     */
    public function getIsOpenInNewWindow()
    {
        return Mage::getStoreConfigFlag(Mage_Downloadable_Model_Link::XML_PATH_TARGET_NEW_WINDOW);
    }
}
