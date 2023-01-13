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
 * @copyright  Copyright (c) 2018-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Validator for custom layout update
 *
 * Validator checked XML validation and protected expressions
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Model_LayoutUpdate_Validator extends Zend_Validate_Abstract
{
    public const XML_INVALID                             = 'invalidXml';
    public const INVALID_TEMPLATE_PATH                   = 'invalidTemplatePath';
    public const INVALID_BLOCK_NAME                      = 'invalidBlockName';
    public const PROTECTED_ATTR_HELPER_IN_TAG_ACTION_VAR = 'protectedAttrHelperInActionVar';

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
    protected $_disallowedXPathExpressions = [];

    /**
     * Disallowed template name
     *
     * @var array
     */
    protected $_disallowedBlock = [];

    /**
     * @var Mage_Core_Model_Layout_Validator
     */
    protected $_validator;

    /**
     * Protected expressions
     *
     * @var array
     */
    protected $_protectedExpressions = [];

    public function __construct()
    {
        $this->_initMessageTemplates();
        $this->_initValidator();
    }

    /**
     * Returns array of validation failure messages
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->_validator->getMessages();
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
        return $this->_validator->isValid($value);
    }

    /**
     * Initialize the validator instance with populated template messages
     */
    protected function _initValidator()
    {
        $this->_validator = Mage::getModel('core/layout_validator');
        $this->_disallowedBlock = $this->_validator->getDisallowedBlocks();
        $this->_protectedExpressions = $this->_validator->getProtectedExpressions();
        $this->_disallowedXPathExpressions = $this->_validator->getDisallowedXpathValidationExpression();
        $this->_validator->setMessages($this->_messageTemplates);
    }

    /**
     * Initialize messages templates with translating
     *
     * @return Mage_Adminhtml_Model_LayoutUpdate_Validator
     */
    protected function _initMessageTemplates()
    {
        if (!$this->_messageTemplates) {
            $this->_messageTemplates = [
                self::PROTECTED_ATTR_HELPER_IN_TAG_ACTION_VAR =>
                    Mage::helper('adminhtml')->__('Helper attributes should not be used in custom layout updates.'),
                self::XML_INVALID => Mage::helper('adminhtml')->__('XML data is invalid.'),
                self::INVALID_TEMPLATE_PATH => Mage::helper('adminhtml')->__(
                    'Invalid template path used in layout update.'
                ),
                self::INVALID_BLOCK_NAME => Mage::helper('adminhtml')->__('Disallowed block name for frontend.'),
                Mage_Core_Model_Layout_Validator::INVALID_XML_OBJECT_EXCEPTION =>
                    Mage::helper('adminhtml')->__('XML object is not instance of "Varien_Simplexml_Element".'),
            ];
        }
        return $this;
    }

    /**
     * Returns xPath for validate incorrect path to template
     *
     * @return string xPath for validate incorrect path to template
     */
    protected function _getXpathValidationExpression()
    {
        return $this->_validator->getXpathValidationExpression();
    }

    /**
     * Returns xPath for validate incorrect block name
     *
     * @return string xPath for validate incorrect block name
     */
    protected function _getXpathBlockValidationExpression()
    {
        return $this->_validator->getXpathBlockValidationExpression();
    }

    /**
     * Validate template path for preventing access to the directory above
     * If template path value has "../" @throws Exception
     *
     * @param array $templatePaths
     */
    protected function _validateTemplatePath(array $templatePaths)
    {
        $this->_validator->validateTemplatePath($templatePaths);
    }
}
