<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Rss
 */

/**
 * Default rss helper
 *
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
