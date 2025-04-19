<?php

declare(strict_types=1);

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Rss
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Rss Observer Model
 *
 * @category   Mage
 * @package    Mage_Rss
 */
class Mage_Rss_Model_Observer_ReviewSaveAfter extends Mage_Rss_Model_Observer_Abstract implements Mage_Core_Observer_Interface
{
    /**
     * Clean cache for catalog review rss
     */
    public function execute(Varien_Event_Observer $observer): self
    {
        $this->_cleanCache(Mage_Rss_Block_Catalog_Review::CACHE_TAG);
        return $this;
    }
}
