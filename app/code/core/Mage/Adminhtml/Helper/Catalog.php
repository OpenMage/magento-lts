<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml Catalog helper
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Helper_Catalog extends Mage_Core_Helper_Abstract
{
    public const XML_PATH_SITEMAP_VALID_PATHS = 'general/file/sitemap_generate_valid_paths';

    protected $_moduleName = 'Mage_Adminhtml';

    /**
     * Attribute Tab block name for product edit
     *
     * @var string
     */
    protected $_attributeTabBlock = null;

    /**
     * Attribute Tab block name for category edit
     *
     * @var string
     */
    protected $_categoryAttributeTabBlock;

    /**
     * Retrieve Attribute Tab Block Name for Product Edit
     *
     * @return string
     */
    public function getAttributeTabBlock()
    {
        return $this->_attributeTabBlock;
    }

    /**
     * Set Custom Attribute Tab Block Name for Product Edit
     *
     * @param string $attributeTabBlock
     * @return $this
     */
    public function setAttributeTabBlock($attributeTabBlock)
    {
        $this->_attributeTabBlock = $attributeTabBlock;
        return $this;
    }

    /**
     * Retrieve Attribute Tab Block Name for Category Edit
     *
     * @return string
     */
    public function getCategoryAttributeTabBlock()
    {
        return $this->_categoryAttributeTabBlock;
    }

    /**
     * Set Custom Attribute Tab Block Name for Category Edit
     *
     * @param string $attributeTabBlock
     * @return $this
     */
    public function setCategoryAttributeTabBlock($attributeTabBlock)
    {
        $this->_categoryAttributeTabBlock = $attributeTabBlock;
        return $this;
    }

    /**
     * Get list valid paths for generate a sitemap XML file
     *
     * @return array
     */
    public function getSitemapValidPaths()
    {
        $path = Mage::getStoreConfig(self::XML_PATH_SITEMAP_VALID_PATHS);
        /** @var Mage_Core_Helper_Data $helper */
        $helper = Mage::helper('core');
        $path = array_merge($path, $helper->getPublicFilesValidPath());
        return $path;
    }
}
