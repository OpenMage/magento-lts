<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Page
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Page layout config model
 *
 * @category   Mage
 * @package    Mage_Page
 */
class Mage_Page_Model_Config
{
    public const XML_PATH_PAGE_LAYOUTS = 'global/page/layouts';
    public const XML_PATH_CMS_LAYOUTS = 'global/cms/layouts';

    /**
     * Available page layouts
     *
     * @var array
     */
    protected $_pageLayouts = null;

    /**
     * Initialize page layouts list
     *
     * @return $this
     */
    protected function _initPageLayouts()
    {
        if ($this->_pageLayouts === null) {
            $this->_pageLayouts = [];
            $this->_appendPageLayouts(self::XML_PATH_CMS_LAYOUTS);
            $this->_appendPageLayouts(self::XML_PATH_PAGE_LAYOUTS);
        }
        return $this;
    }

    /**
     * Fill in $_pageLayouts by reading layouts from config
     *
     * @param string $xmlPath XML path to layouts root
     * @return $this
     */
    protected function _appendPageLayouts($xmlPath)
    {
        if (!Mage::getConfig()->getNode($xmlPath)) {
            return $this;
        }
        if (!is_array($this->_pageLayouts)) {
            $this->_pageLayouts = [];
        }
        foreach (Mage::getConfig()->getNode($xmlPath)->children() as $layoutCode => $layoutConfig) {
            $this->_pageLayouts[$layoutCode] = new Varien_Object([
                'label'         => Mage::helper('page')->__((string)$layoutConfig->label),
                'code'          => $layoutCode,
                'template'      => (string)$layoutConfig->template,
                'layout_handle' => (string)$layoutConfig->layout_handle,
                'is_default'    => (int)$layoutConfig->is_default,
            ]);
        }
        return $this;
    }

    /**
     * Retrieve available page layouts
     *
     * @return array
     */
    public function getPageLayouts()
    {
        $this->_initPageLayouts();
        return $this->_pageLayouts;
    }

    /**
     * Retrieve page layout by code
     *
     * @param string $layoutCode
     * @return Varien_Object|false
     */
    public function getPageLayout($layoutCode)
    {
        $this->_initPageLayouts();

        return $this->_pageLayouts[$layoutCode] ?? false;
    }

    /**
     * Retrieve page layout handles
     *
     * @return array
     */
    public function getPageLayoutHandles()
    {
        $handles = [];

        foreach ($this->getPageLayouts() as $layout) {
            $handles[$layout->getCode()] = $layout->getLayoutHandle();
        }

        return $handles;
    }
}
