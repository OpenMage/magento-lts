<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Base html block
 *
 * @package    Mage_Core
 *
 * @method $this setContentHeading(string $value)
 * @method $this setDestElementId(string $value)
 * @method $this setDisplayMinimalPrice(bool $value)
 * @method $this setFormAction(string $value)
 * @method $this setIdSuffix(string $value)
 * @method $this setProduct(Mage_Catalog_Model_Product $value)
 */
class Mage_Core_Block_Template extends Mage_Core_Block_Abstract
{
    public const XML_PATH_DEBUG_TEMPLATE_HINTS_ADMIN        = 'dev/debug/template_hints_admin';

    public const XML_PATH_DEBUG_TEMPLATE_HINTS_BLOCKS_ADMIN = 'dev/debug/template_hints_blocks_admin';

    public const XML_PATH_DEBUG_TEMPLATE_HINTS              = 'dev/debug/template_hints';

    public const XML_PATH_DEBUG_TEMPLATE_HINTS_BLOCKS       = 'dev/debug/template_hints_blocks';

    public const XML_PATH_TEMPLATE_ALLOW_SYMLINK            = 'dev/template/allow_symlink';

    /**
     * View scripts directory
     *
     * @var string
     */
    protected $_viewDir = '';

    /**
     * Assigned variables for view
     *
     * @var array
     */
    protected $_viewVars = [];

    protected $_baseUrl;

    protected $_jsUrl;

    protected static $_showTemplateHintsAdmin;

    protected static $_showTemplateHintsBlocksAdmin;

    protected static $_showTemplateHints;

    protected static $_showTemplateHintsBlocks;

    /**
     * Path to template file in theme.
     *
     * @var string
     */
    protected $_template = '';

    /**
     * Internal constructor, that is called from real constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        /*
         * In case template was passed through constructor
         * we assign it to block's property _template
         * Mainly for those cases when block created
         * not via Mage_Core_Model_Layout::addBlock()
         */
        if ($this->hasData('template')) {
            $this->setTemplate($this->getData('template'));
        }
    }

    /**
     * Get relevant path to template
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->_template;
    }

    /**
     * Set path to template used for generating block's output.
     *
     * @param string $template
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->_template = $template;
        return $this;
    }

    /**
     * Get absolute path to template
     *
     * @return string
     */
    public function getTemplateFile()
    {
        $params = ['_relative' => true];
        $area = $this->getArea();
        if ($area) {
            $params['_area'] = $area;
        }

        return Mage::getDesign()->getTemplateFilename($this->getTemplate(), $params);
    }

    /**
     * Get design area
     * @return string
     */
    public function getArea()
    {
        return $this->_getData('area');
    }

    /**
     * Assign variable
     *
     * @param   array|string $key
     * @param   mixed $value
     * @return  $this
     */
    public function assign($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->assign($k, $v);
            }
        } else {
            $this->_viewVars[$key] = $value;
        }

        return $this;
    }

    /**
     * Set template location directory
     *
     * @param string $dir
     * @return $this
     */
    public function setScriptPath($dir)
    {
        if (!str_contains($dir, '..') && ($dir === Mage::getBaseDir('design') || str_starts_with(realpath($dir), realpath(Mage::getBaseDir('design'))))) {
            $this->_viewDir = $dir;
        } else {
            Mage::log('Not valid script path:' . $dir, Zend_Log::CRIT, null, true);
        }

        return $this;
    }

    /**
     * Check if direct output is allowed for block
     *
     * @return bool
     */
    public function getDirectOutput()
    {
        if ($this->getLayout()) {
            return $this->getLayout()->getDirectOutput();
        }

        return false;
    }

    /**
     * @return bool
     */
    public function getShowTemplateHintsAdmin()
    {
        if (is_null(self::$_showTemplateHintsAdmin)) {
            self::$_showTemplateHintsAdmin = Mage::getStoreConfig(self::XML_PATH_DEBUG_TEMPLATE_HINTS_ADMIN)
                && Mage::helper('core')->isDevAllowed();
            self::$_showTemplateHintsBlocksAdmin = Mage::getStoreConfig(self::XML_PATH_DEBUG_TEMPLATE_HINTS_BLOCKS_ADMIN)
                && Mage::helper('core')->isDevAllowed();
        }

        return self::$_showTemplateHintsAdmin;
    }

    /**
     * @return bool
     */
    public function getShowTemplateHints()
    {
        if (is_null(self::$_showTemplateHints)) {
            self::$_showTemplateHints = Mage::getStoreConfig(self::XML_PATH_DEBUG_TEMPLATE_HINTS)
                && Mage::helper('core')->isDevAllowed();
            self::$_showTemplateHintsBlocks = Mage::getStoreConfig(self::XML_PATH_DEBUG_TEMPLATE_HINTS_BLOCKS)
                && Mage::helper('core')->isDevAllowed();
        }

        return self::$_showTemplateHints;
    }

    /**
     * Retrieve block cache status
     */
    private function _getCacheHintStatusColor(): string
    {
        if (!is_null($this->getCacheLifetime())) {
            return 'green';
        } else {
            $currentParentBlock = $this;
            $i = 0;
            while ($i++ < 20 && $currentParentBlock instanceof Mage_Core_Block_Abstract) {
                if (!is_null($currentParentBlock->getCacheLifetime())) {
                    return 'orange'; // not cached, but within cached
                }

                $currentParentBlock = $currentParentBlock->getParentBlock();
            }
        }

        return 'red';
    }

    /**
     * Retrieve block view from file (template)
     *
     * @param   string $fileName
     * @return  string
     */
    public function fetchView($fileName)
    {
        Varien_Profiler::start($fileName);

        // EXTR_SKIP protects from overriding
        // already defined variables
        extract($this->_viewVars, EXTR_SKIP);
        $do = $this->getDirectOutput();

        $hints = Mage::app()->getStore()->isAdmin() ? $this->getShowTemplateHintsAdmin() : $this->getShowTemplateHints();

        if (!$do) {
            ob_start();
        }

        if ($hints) {
            $cacheHintStatusColor = $this->_getCacheHintStatusColor();
            echo <<<HTML
<div style="position:relative; border:1px dotted {$cacheHintStatusColor}; margin:6px 2px; padding:18px 2px 2px 2px; zoom:1;">
<div style="position:absolute; left:0; top:0; padding:2px 5px; background:{$cacheHintStatusColor}; color:white; font:normal 11px Arial;
text-align:left !important; z-index:998;text-transform: none;" onmouseover="this.style.zIndex='999'"
onmouseout="this.style.zIndex='998'" title="{$fileName}">{$fileName}</div>
HTML;
            if (Mage::app()->getStore()->isAdmin() ? self::$_showTemplateHintsBlocksAdmin : self::$_showTemplateHintsBlocks) {
                $thisClass = static::class;
                echo <<<HTML
<div style="position:absolute; right:0; top:0; padding:2px 5px; background:{$cacheHintStatusColor}; color:blue; font:normal 11px Arial;
text-align:left !important; z-index:998;text-transform: none;" onmouseover="this.style.zIndex='999'" onmouseout="this.style.zIndex='998'"
title="{$thisClass}">{$thisClass}</div>
HTML;
            }
        }

        try {
            if (!str_contains($this->_viewDir . DS . $fileName, '..')
                && ($this->_viewDir == Mage::getBaseDir('design') || str_starts_with(realpath($this->_viewDir), realpath(Mage::getBaseDir('design'))))
            ) {
                include $this->_viewDir . DS . $fileName;
            } else {
                $thisClass = static::class;
                Mage::log('Not valid template file:' . $fileName . ' class: ' . $thisClass, Zend_Log::CRIT, null, true);
            }
        } catch (Throwable $throwable) {
            if (!$do) {
                ob_get_clean();
                $do = true;
            }

            if (Mage::getIsDeveloperMode()) {
                throw $throwable;
            }

            Mage::logException($throwable);
        }

        if ($hints) {
            echo '</div>';
        }

        if (!$do) {
            $html = ob_get_clean();
        } else {
            $html = '';
        }

        Varien_Profiler::stop($fileName);
        return $html;
    }

    /**
     * Render block
     *
     * @return string
     */
    public function renderView()
    {
        $this->setScriptPath(Mage::getBaseDir('design'));
        return $this->fetchView($this->getTemplateFile());
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->getTemplate()) {
            return '';
        }

        return $this->renderView();
    }

    /**
     * Get base url of the application
     *
     * @return string
     */
    public function getBaseUrl()
    {
        if (!$this->_baseUrl) {
            $this->_baseUrl = Mage::getBaseUrl();
        }

        return $this->_baseUrl;
    }

    /**
     * Get url of base javascript file
     *
     * To get url of skin javascript file use getSkinUrl()
     *
     * @param string $fileName
     * @return string
     */
    public function getJsUrl($fileName = '')
    {
        if (!$this->_jsUrl) {
            $this->_jsUrl = Mage::getBaseUrl('js');
        }

        return $this->_jsUrl . $fileName;
    }

    /**
     * Get data from specified object
     *
     * @param string $key
     * @return mixed
     */
    public function getObjectData(Varien_Object $object, $key)
    {
        return $object->getDataUsingMethod((string) $key);
    }

    /**
     * @inheritDoc
     */
    public function getCacheKeyInfo()
    {
        return [
            'BLOCK_TPL',
            Mage::app()->getStore()->getCode(),
            $this->getTemplateFile(),
            'template' => $this->getTemplate(),
        ];
    }

    /**
     * Get is allowed symlinks flag
     *
     * @return bool
     * @deprecated
     */
    protected function _getAllowSymlinks()
    {
        return false;
    }
}
