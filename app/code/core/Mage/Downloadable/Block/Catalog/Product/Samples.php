<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Downloadable
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Downloadable Product Samples part block
 *
 * @category   Mage
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
