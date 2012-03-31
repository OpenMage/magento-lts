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
 * @category    Mage
 * @package     Mage_Api2
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API2 EAV Validator
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Resource_Validator_Eav extends Mage_Api2_Model_Resource_Validator
{
    /**
     * Config node key of current validator
     */
    const CONFIG_NODE_KEY = 'eav';

    /**
     * Form path
     *
     * @var string
     */
    protected $_formPath;

    /**
     * Entity model
     *
     * @var Mage_Core_Model_Abstract
     */
    protected $_entity;

    /**
     * Form code
     *
     * @var string
     */
    protected $_formCode;

    /**
     * Eav form model
     *
     * @var Mage_Eav_Model_Form
     */
    protected $_eavForm;

    /**
     * Construct. Set all depends.
     *
     * Required parameteres for options:
     * - resource
     *
     * @param array $options
     * @throws Exception If passed parameter 'resource' is wrong
     * @throws Exception If config parameter 'formPath' is empty
     * @throws Exception If config parameter 'formCode' is empty
     * @throws Exception If config parameter 'entity' is wrong
     * @throws Exception If entity is not model
     * @throws Exception If eav form is not found
     */
    public function __construct($options)
    {
        if (!isset($options['resource']) || !$options['resource'] instanceof Mage_Api2_Model_Resource) {
            throw new Exception("Passed parameter 'resource' is wrong.");
        }
        $resource = $options['resource'];
        $userType = $resource->getUserType();

        $validationConfig = $resource->getConfig()->getValidationConfig(
            $resource->getResourceType(), self::CONFIG_NODE_KEY);

        if (empty($validationConfig[$userType]['form_model'])) {
            throw new Exception("Config parameter 'formPath' is empty.");
        }
        $this->_formPath = $validationConfig[$userType]['form_model'];

        if (empty($validationConfig[$userType]['form_code'])) {
            throw new Exception("Config parameter 'formCode' is empty.");
        }
        $this->_formCode = $validationConfig[$userType]['form_code'];

        if (empty($validationConfig[$userType]['entity_model'])) {
            throw new Exception("Config parameter 'entity' is wrong.");
        }
        $this->_entity = Mage::getModel($validationConfig[$userType]['entity_model']);
        if (empty($this->_entity) || !$this->_entity instanceof Mage_Core_Model_Abstract) {
            throw new Exception("Entity is not model.");
        }

        $this->_eavForm = Mage::getModel($this->_formPath);
        if (empty($this->_eavForm) || !$this->_eavForm instanceof Mage_Eav_Model_Form) {
            throw new Exception("Eav form '{$this->_formPath}' is not found.");
        }
        $this->_eavForm->setEntity($this->_entity)
            ->setFormCode($this->_formCode)
            ->ignoreInvisible(false);
    }

    /**
     * Filter request data.
     *
     * @param  array $data
     * @return array Filtered data
     */
    public function filter(array $data)
    {
        return array_intersect_key($this->_eavForm->extractData($this->_eavForm->prepareRequest($data)), $data);
    }

    /**
     * Validate entity.
     * If the $partial parameter is TRUE, then we validate only those parameters that were passed.
     *
     * If fails validation, then this method returns false, and
     * getErrors() will return an array of errors that explain why the
     * validation failed.
     *
     * @param array $data
     * @param bool $partial
     * @return bool
     */
    public function isValidData(array $data, $partial = false)
    {
        $errors = array();
        foreach ($this->_eavForm->getAttributes() as $attribute) {
            if ($partial && !array_key_exists($attribute->getAttributeCode(), $data)) {
                continue;
            }
            if ($this->_eavForm->ignoreInvisible() && !$attribute->getIsVisible()) {
                continue;
            }
            if (!isset($data[$attribute->getAttributeCode()])) {
                $data[$attribute->getAttributeCode()] = null;
            }

            $result = Mage_Eav_Model_Attribute_Data::factory($attribute, $this->_eavForm->getEntity())
                ->setExtractedData($data)->validateValue($data[$attribute->getAttributeCode()]);
            if ($result !== true) {
                $errors = array_merge($errors, $result);
            }
        }

        if (count($errors)) {
            $this->_setErrors($errors);
            return false;
        }

        return true;
    }
}
