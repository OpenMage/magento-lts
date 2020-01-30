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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Validator for custom layout update
 *
 * Validator checked XML validation and protected expressions
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Layout_Validator extends Zend_Validate_Abstract
{
    const XML_PATH_LAYOUT_DISALLOWED_BLOCKS       = 'validators/custom_layout/disallowed_block';
    const XML_INVALID                             = 'invalidXml';
    const INVALID_TEMPLATE_PATH                   = 'invalidTemplatePath';
    const INVALID_BLOCK_NAME                      = 'invalidBlockName';
    const PROTECTED_ATTR_HELPER_IN_TAG_ACTION_VAR = 'protectedAttrHelperInActionVar';
    const INVALID_XML_OBJECT_EXCEPTION            = 'invalidXmlObject';

    /**
     * The Varien SimpleXml object
     *
     * @var Varien_Simplexml_Element
     */
    protected $_value;

    /**
     * XPath expression for checking layout update
     *
     * @var array
     */
    protected $_disallowedXPathExpressions = array(
        '*//template',
        '*//@template',
        '//*[@method=\'setTemplate\']',
        '//*[@method=\'setDataUsingMethod\']//*[contains(translate(text(),
        \'ABCDEFGHIJKLMNOPQRSTUVWXYZ\', \'abcdefghijklmnopqrstuvwxyz\'), \'template\')]/../*',
    );

    /**
     * @var string
     */
    protected $_xpathBlockValidationExpression = '';

    /**
     * Disallowed template name
     *
     * @var array
     */
    protected $_disallowedBlock = array();

    /**
     * Protected expressions
     *
     * @var array
     */
    protected $_protectedExpressions = array(
        self::PROTECTED_ATTR_HELPER_IN_TAG_ACTION_VAR => '//action/*[@helper]',
    );

    /**
     * Construct
     */
    public function __construct()
    {
        $this->_initMessageTemplates();
        $this->getDisallowedBlocks();
    }

    /**
     * Initialize messages templates with translating
     *
     * @return Mage_Core_Model_Layout_Validator
     */
    protected function _initMessageTemplates()
    {
        if (!$this->_messageTemplates) {
            $this->_messageTemplates = array(
                self::PROTECTED_ATTR_HELPER_IN_TAG_ACTION_VAR =>
                    Mage::helper('core')->__('Helper attributes should not be used in custom layout updates.'),
                self::XML_INVALID => Mage::helper('core')->__('XML data is invalid.'),
                self::INVALID_TEMPLATE_PATH => Mage::helper('core')->__(
                    'Invalid template path used in layout update.'
                ),
                self::INVALID_BLOCK_NAME => Mage::helper('core')->__('Disallowed block name for frontend.'),
                self::INVALID_XML_OBJECT_EXCEPTION =>
                    Mage::helper('core')->__('XML object is not instance of "Varien_Simplexml_Element".'),
            );
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getDisallowedBlocks()
    {
        if (!count($this->_disallowedBlock)) {
            $disallowedBlockConfig = $this->_getDisallowedBlockConfigValue();
            if (is_array($disallowedBlockConfig)) {
                foreach ($disallowedBlockConfig as $blockName => $value) {
                    $this->_disallowedBlock[] = $blockName;
                }
            }
        }
        return $this->_disallowedBlock;
    }

    /**
     * @return mixed
     */
    protected function _getDisallowedBlockConfigValue()
    {
        return Mage::getStoreConfig(self::XML_PATH_LAYOUT_DISALLOWED_BLOCKS);
    }

    /**
     * Returns true if and only if $value meets the validation requirements
     *
     * If $value fails validation, then this method returns false, and
     * getMessages() will return an array of messages that explain why the
     * validation failed.
     *
     * @throws Exception            Throw exception when xml object is not
     *                              instance of Varien_Simplexml_Element
     * @param Varien_Simplexml_Element|string $value
     * @return bool
     */
    public function isValid($value)
    {
        if (is_string($value)) {
            $value = trim($value);
            try {
                $value = new Varien_Simplexml_Element('<config>' . $value . '</config>');
            } catch (Exception $e) {
                $this->_error(self::XML_INVALID);
                return false;
            }
        } elseif (!($value instanceof Varien_Simplexml_Element)) {
            throw new Exception($this->_messageTemplates[self::INVALID_XML_OBJECT_EXCEPTION]);
        }
        if ($value->xpath($this->getXpathBlockValidationExpression())) {
            $this->_error(self::INVALID_BLOCK_NAME);
            return false;
        }
        // if layout update declare custom templates then validate their paths
        if ($templatePaths = $value->xpath($this->getXpathValidationExpression())) {
            try {
                $this->validateTemplatePath($templatePaths);
            } catch (Exception $e) {
                $this->_error(self::INVALID_TEMPLATE_PATH);
                return false;
            }
        }
        $this->_setValue($value);

        foreach ($this->_protectedExpressions as $key => $xpr) {
            if ($this->_value->xpath($xpr)) {
                $this->_error($key);
                return false;
            }
        }
        return true;
    }

    /**
     * @return array
     */
    public function getProtectedExpressions()
    {
        return $this->_protectedExpressions;
    }

    /**
     * Returns xPath for validate incorrect path to template
     *
     * @return string xPath for validate incorrect path to template
     */
    public function getXpathValidationExpression()
    {
        return implode(" | ", $this->_disallowedXPathExpressions);
    }

    /**
     * @return array
     */
    public function getDisallowedXpathValidationExpression()
    {
        return $this->_disallowedXPathExpressions;
    }

    /**
     * Returns xPath for validate incorrect block name
     *
     * @return string xPath for validate incorrect block name
     */
    public function getXpathBlockValidationExpression()
    {
        if (!$this->_xpathBlockValidationExpression) {
            if (count($this->_disallowedBlock)) {
                foreach ($this->_disallowedBlock as $key => $value) {
                    $this->_xpathBlockValidationExpression .= $key > 0 ? " | " : '';
                    $this->_xpathBlockValidationExpression .=
                        "//block[translate(@type, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz') = ";
                    $this->_xpathBlockValidationExpression .=
                        "translate('$value', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz')]";
                }
            }
        }
        return $this->_xpathBlockValidationExpression;
    }

    /**
     * Validate template path for preventing access to the directory above
     * If template path value has "../"
     *
     * @throws Exception
     *
     * @param $templatePaths | array
     */
    public function validateTemplatePath(array $templatePaths)
    {
        /** @var $path Varien_Simplexml_Element */
        foreach ($templatePaths as $path) {
            if ($path->hasChildren()) {
                $path = stripcslashes(trim((string) $path->children(), '"'));
            }
            if (strpos($path, '..' . DS) !== false) {
                throw new Exception();
            }
        }
    }
}
