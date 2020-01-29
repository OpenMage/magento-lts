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
 * @package     Mage_SalesRule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 *Rule product condition attribute data model
 *
 * @category Mage
 * @package Mage_SalesRule
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Mage_SalesRule_Model_Rule_Condition_Product_Attribute_Assigned extends Mage_Rule_Model_Condition_Product_Abstract
{
    /**
     * The operator type which indicates whether the attribute was assigned
     */
    const OPERATOR_ATTRIBUTE_IS_ASSIGNED = 'is_assigned';

    /**
     * The operator type which indicates whether the attribute was not assigned
     */
    const OPERATOR_ATTRIBUTE_IS_NOT_ASSIGNED = 'is_not_assigned';

    /**
     * A default operator code
     */
    const DEFAULT_OPERATOR = self::OPERATOR_ATTRIBUTE_IS_ASSIGNED;

    /**
     * Operator select options hash
     * @var array
     */
    protected $_operatorSelectOptionsHash = null;

    /**
     * A cached options list
     * @var array
     */
    protected $_cachedOperatorSelectOptionsCache = null;

    /**
     * Initialize and retrieve a helper instance
     * @return Mage_SalesRule_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('salesrule');
    }

    /**
     * Retrieve a product instance and initialize if needed
     * @param Varien_Object $object
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct(Varien_Object $object)
    {
        return $object->getProduct() instanceof Mage_Catalog_Model_Product
            ? $object->getProduct()
            : Mage::getModel('catalog/product')->load($object->getProductId());
    }

    /**
     * Initialize options hash
     */
    public function __construct()
    {
        $this->_operatorSelectOptionsHash = array(
            self::OPERATOR_ATTRIBUTE_IS_ASSIGNED        => $this->_getHelper()->__('is assigned'),
            self::OPERATOR_ATTRIBUTE_IS_NOT_ASSIGNED    => $this->_getHelper()->__('is not assigned')
        );

        parent::__construct();
    }

    /**
     * Retrieves unary operators of the attribute assignment state
     * @return array
     */
    public function getOperatorSelectOptions()
    {
        if (is_null($this->_cachedOperatorSelectOptionsCache)) {
            $this->_cachedOperatorSelectOptionsCache = array();
            foreach ($this->_operatorSelectOptionsHash as $operatorValue => $operatorLabel) {
                $this->_cachedOperatorSelectOptionsCache[] = array(
                    'label' => $operatorLabel,
                    'value' => $operatorValue
                );
            }
        }

        return $this->_cachedOperatorSelectOptionsCache;
    }

    /**
     * Retrieve an operator name
     * @return string
     */
    public function getOperatorName()
    {
        return $this->getOperator() && array_key_exists($this->getOperator(), $this->_operatorSelectOptionsHash)
            ? $this->_operatorSelectOptionsHash[$this->getOperator()]
            : $this->_operatorSelectOptionsHash[self::DEFAULT_OPERATOR];
    }

    /**
     * Validate a product, check whether the attribute is assigned to the product
     * @param Varien_Object $object
     *
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        $product    = $this->_getProduct($object);
        $attributes = $product->getAttributes();

        return $this->getOperator() == self::OPERATOR_ATTRIBUTE_IS_ASSIGNED
            && array_key_exists($this->getAttribute(), $attributes)
            || $this->getOperator() == self::OPERATOR_ATTRIBUTE_IS_NOT_ASSIGNED
            && !array_key_exists($this->getAttribute(), $attributes);
    }


    /**
     * Generate a condition html
     * @return string
     */
    public function asHtml()
    {
        return $this->_getHelper()->__(
            'Attribute "%s" %s %s %s',
            $this->getAttributeElementHtml(),
            $this->getOperatorElementHtml(),
            $this->getRemoveLinkHtml(),
            $this->getTypeElementHtml()
        );
    }
}
