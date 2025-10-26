<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Cms
 */

/**
 * Widget to display link to CMS page
 *
 * @package    Mage_Cms
 */
class Mage_Cms_Block_Widget_Page_Link extends Mage_Core_Block_Html_Link implements Mage_Widget_Block_Interface
{
    /**
     * Prepared href attribute
     *
     * @var string|null
     */
    protected $_href;

    /**
     * Prepared title attribute
     *
     * @var string
     */
    protected $_title;

    /**
     * Prepared anchor text
     *
     * @var string
     */
    protected $_anchorText;

    /**
     * Prepare page url. Use passed identifier
     * or retrieve such using passed page id.
     *
     * @return string
     */
    public function getHref()
    {
        if (!$this->_href) {
            $this->_href = '';
            if ($this->getDataByKey('href')) {
                $this->_href = $this->getDataByKey('href');
            } elseif ($this->getDataByKey('page_id')) {
                $this->_href = Mage::helper('cms/page')->getPageUrl($this->getDataByKey('page_id'));
            }
        }

        return $this->_href;
    }

    /**
     * Prepare anchor title attribute using passed title
     * as parameter or retrieve page title from DB using passed identifier or page id.
     *
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getTitle()
    {
        if (!$this->_title) {
            $this->_title = '';
            if ($this->getDataByKey('title') !== null) {
                // compare to null used here bc user can specify blank title
                $this->_title = $this->getDataByKey('title');
            } elseif ($this->getDataByKey('page_id')) {
                $this->_title = $this->getCmsPageTitleById($this->getDataByKey('page_id'));
            } elseif ($this->getDataByKey('href')) {
                $this->_title = $this->getCmsPageTitleByIdentifier($this->getDataByKey('href'));
            }
        }

        return $this->_title;
    }

    /**
     * Prepare anchor text using passed text as parameter.
     * If anchor text was not specified use title instead and
     * if title will be blank string, page identifier will be used.
     *
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getAnchorText()
    {
        if ($this->getDataByKey('anchor_text')) {
            $this->_anchorText = $this->getDataByKey('anchor_text');
        } elseif ($this->getTitle()) {
            $this->_anchorText = $this->getTitle();
        } elseif ($this->getDataByKey('href')) {
            $this->_anchorText = $this->getCmsPageTitleByIdentifier($this->getDataByKey('href'));
        } elseif ($this->getDataByKey('page_id')) {
            $this->_anchorText = $this->getCmsPageTitleById($this->getDataByKey('page_id'));
        } else {
            $this->_anchorText = $this->getDataByKey('href');
        }

        return $this->_anchorText;
    }

    protected function getCmsPageTitleById(int|string $pageId): string
    {
        return Mage::getResourceSingleton('cms/page')->getCmsPageTitleById($pageId);
    }

    /**
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function getCmsPageTitleByIdentifier(int|string $identifier): string
    {
        return Mage::getResourceSingleton('cms/page')->setStore(Mage::app()->getStore())
            ->getCmsPageTitleByIdentifier($identifier);
    }
}
