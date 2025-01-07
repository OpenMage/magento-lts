<?php

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
 * @copyright  Copyright (c) 2021-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Default rss helper
 *
 * @category   Mage
 * @package    Mage_Rss
 */
class Mage_Rss_Helper_Catalog extends Mage_Core_Helper_Abstract
{
    protected $_moduleName = 'Mage_Rss';

    /**
     * @return string
     */
    public function getTagFeedUrl()
    {
        $url = '';
        if (Mage::getStoreConfig('rss/catalog/tag') && $this->_getRequest()->getParam('tagId')) {
            $tagModel = Mage::getModel('tag/tag')->load($this->_getRequest()->getParam('tagId'));
            if ($tagModel->getId()) {
                return Mage::getUrl('rss/catalog/tag', ['tagName' => urlencode($tagModel->getName())]);
            }
        }
        return $url;
    }
}
