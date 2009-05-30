<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Core Email Template Filter Model
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Email_Template_Filter extends Varien_Filter_Template
{
    /**
     * Use absolute links flag
     *
     * @var bool
     */
    protected $_useAbsoluteLinks = false;

    /**
     * Use session in URL flag
     *
     * @var bool
     */
    protected $_useSessionInUrl;

    /**
     * Url Instance
     *
     * @var Mage_Core_Model_Url
     */
    protected static $_urlInstance;

    /**
     * Set use absolute links flag
     *
     * @param bool $flag
     * @return Mage_Core_Model_Email_Template_Filter
     */
    public function setUseAbsoluteLinks($flag)
    {
        $this->_useAbsoluteLinks = $flag;
        return $this;
    }

    /**
     * Set Use session in URL flag
     *
     * @param bool $flag
     * @return Mage_Core_Model_Email_Template_Filter
     */
    public function setUseSessionInUrl($flag)
    {
        $this->_useSessionInUrl = (bool)$flag;
        return $this;
    }

    /**
     * Retrieve Block html directive
     *
     * @param array $construction
     * @return string
     */
    public function blockDirective($construction)
    {
        $skipParams = array('type', 'id', 'output');
        $blockParameters = $this->_getIncludeParameters($construction[2]);
        $layout = Mage::app()->getLayout();

        if (isset($blockParameters['type'])) {
            $type = $blockParameters['type'];
            $block = $layout->createBlock($type, null, $blockParameters);
        } elseif (isset($blockParameters['id'])) {
            $block = $layout->createBlock('cms/block');
            if ($block) {
                $block->setBlockId($blockParameters['id'])
                    ->setBlockParams($blockParameters);
                foreach ($blockParameters as $k => $v) {
                    if (in_array($k, $skipParams)) {
                        continue;
                    }
                    $block->setDataUsingMethod($k, $v);
                }
            }
        }
        if (!$block) {
            return '';
        }
        if (isset($blockParameters['output'])) {
            $method = $blockParameters['output'];
        }
        if (!isset($method) || !is_string($method) || !is_callable(array($block, $method))) {
            $method = 'toHtml';
        }
        return $block->$method();
    }

    /**
     * Retrieve layout html directive
     *
     * @param array $construction
     * @return string
     */
    public function layoutDirective($construction)
    {
        $skipParams = array('handle', 'area');

        $params = $this->_getIncludeParameters($construction[2]);
        $layout = Mage::getModel('core/layout');
        /* @var $layout Mage_Core_Model_Layout */
        if (isset($params['area'])) {
            $layout->setArea($params['area']);
        }
        else {
            $layout->setArea(Mage::app()->getLayout()->getArea());
        }

        $layout->getUpdate()->addHandle($params['handle']);
        $layout->getUpdate()->load();

        $layout->generateXml();
        $layout->generateBlocks();

        foreach ($layout->getAllBlocks() as $blockName => $block) {
            /* @var $block Mage_Core_Block_Abstract */
            foreach ($params as $k => $v) {
                if (in_array($k, $skipParams)) {
                    continue;
                }

                $block->setDataUsingMethod($k, $v);
                $layout->addOutputBlock($blockName);
            }
        }

        $layout->setDirectOutput(false);
        return $layout->getOutput();
    }

    /**
     * Retrieve block parameters
     *
     * @param mixed $value
     * @return array
     */
    protected function _getBlockParameters($value)
    {
        $tokenizer = new Varien_Filter_Template_Tokenizer_Parameter();
        $tokenizer->setString($value);

        return $tokenizer->tokenize();
    }

    /**
     * Retrieve Skin URL directive
     *
     * @param array $construction
     * @return string
     */
    public function skinDirective($construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);
        $params['_absolute'] = $this->_useAbsoluteLinks;

        $url = Mage::getDesign()->getSkinUrl($params['url'], $params);

        return $url;
    }

    /**
     * Retrieve media file URL directive
     *
     * @param array $construction
     * @return string
     */
    public function mediaDirective($construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);
        return Mage::getBaseUrl('media') . $params['url'];
    }

    /**
     * Retrieve store URL directive
     * Support url and direct_url properties
     *
     * @param array $construction
     * @return string
     */
    public function storeDirective($construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);
        if (!isset($params['_query'])) {
            $params['_query'] = array();
        }
        foreach ($params as $k => $v) {
            if (strpos($k, '_query_') === 0) {
                $params['_query'][substr($k, 7)] = $v;
                unset($params[$k]);
            }
        }
        $params['_absolute'] = $this->_useAbsoluteLinks;

        if ($this->_useSessionInUrl === false) {
            $params['_nosid'] = true;
        }

        if (isset($params['direct_url'])) {
            $path = '';
            $params['_direct'] = $params['direct_url'];
            unset($params['direct_url']);
        }
        else {
            $path = $params['url'];
            unset($params['url']);
        }

        if (!self::$_urlInstance) {
            self::$_urlInstance = Mage::getModel('core/url')->setStore(
                Mage::app()->getStore(Mage::getDesign()->getStore())->getId()
            );
        }
        $_urlInstanceOldStore = null;
        if (!empty($path) && !Mage::getStoreConfigFlag(Mage_Core_Model_Store::XML_PATH_STORE_IN_URL)
            && !Mage::app()->isSingleStoreMode())
        {
            $params['_query']['___store'] = Mage::app()->getStore(Mage::getDesign()->getStore())->getCode();
        } elseif (!empty($path) && Mage::getStoreConfigFlag(Mage_Core_Model_Store::XML_PATH_STORE_IN_URL)
            && !Mage::app()->isSingleStoreMode())
        {
            $_urlInstanceOldStore = self::$_urlInstance->getStore();
            self::$_urlInstance->setStore(Mage::app()->getStore(Mage::getDesign()->getStore())->getCode());
        }

        $url = self::$_urlInstance->getUrl($path, $params);
        if (null ==! $_urlInstanceOldStore) {
            self::$_urlInstance->setStore($_urlInstanceOldStore);
        }

        return $url;
    }

    /**
     * Directive for converting special characters to HTML entities
     * Supported options:
     *     allowed_tags - Comma separated html tags that have not to be converted
     *
     * @param array $construction
     * @return string
     */
    public function htmlescapeDirective($construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);
        if (!isset($params['var'])) {
            return '';
        }

        $allowedTags = null;
        if (isset($params['allowed_tags'])) {
            $allowedTags = preg_split('/\s*\,\s*/', $params['allowed_tags'], 0, PREG_SPLIT_NO_EMPTY);
        }

        return Mage::helper('core')->htmlEscape($params['var'], $allowedTags);
    }
}
