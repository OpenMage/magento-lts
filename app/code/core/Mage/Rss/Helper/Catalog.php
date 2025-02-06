<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Rss
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
