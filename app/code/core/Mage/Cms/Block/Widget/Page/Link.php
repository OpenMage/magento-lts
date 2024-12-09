<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Cms
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Widget to display link to CMS page
 *
 * @category   Mage
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
            if ($this->getData('href')) {
                $this->_href = $this->getData('href');
            } elseif ($this->getData('page_id')) {
                $this->_href = Mage::helper('cms/page')->getPageUrl($this->getData('page_id'));
            }
        }

        return $this->_href;
    }

    /**
     * Prepare anchor title attribute using passed title
     * as parameter or retrieve page title from DB using passed identifier or page id.
     *
     * @return string
     */
    public function getTitle()
    {
        if (!$this->_title) {
            $this->_title = '';
            if ($this->getData('title') !== null) {
                // compare to null used here bc user can specify blank title
                $this->_title = $this->getData('title');
            } elseif ($this->getData('page_id')) {
                $this->_title = Mage::getResourceSingleton('cms/page')->getCmsPageTitleById($this->getData('page_id'));
            } elseif ($this->getData('href')) {
                $this->_title = Mage::getResourceSingleton('cms/page')->setStore(Mage::app()->getStore())
                    ->getCmsPageTitleByIdentifier($this->getData('href'));
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
     */
    public function getAnchorText()
    {
        if ($this->getData('anchor_text')) {
            $this->_anchorText = $this->getData('anchor_text');
        } elseif ($this->getTitle()) {
            $this->_anchorText = $this->getTitle();
        } elseif ($this->getData('href')) {
            $this->_anchorText = Mage::getResourceSingleton('cms/page')->setStore(Mage::app()->getStore())
                ->getCmsPageTitleByIdentifier($this->getData('href'));
        } elseif ($this->getData('page_id')) {
            $this->_anchorText = Mage::getResourceSingleton('cms/page')->getCmsPageTitleById($this->getData('page_id'));
        } else {
            $this->_anchorText = $this->getData('href');
        }

        return $this->_anchorText;
    }
}
