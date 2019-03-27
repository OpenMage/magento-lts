<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Validator for Email Template
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Model_Email_PathValidator extends Zend_Validate_Abstract
{
    /**
     * Returns true if and only if $value meets the validation requirements
     * If $value fails validation, then this method returns false
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
        $pathNode = is_array($value) ? array_shift($value) : $value;

        return $this->isEncryptedNodePath($pathNode);
    }

    /**
     * Return bool after checking the encrypted model in the path to config node
     *
     * @param string $path
     * @return bool
     */
    protected function isEncryptedNodePath($path)
    {
        /** @var $configModel Mage_Adminhtml_Model_Config */
        $configModel = Mage::getSingleton('adminhtml/config');

        return in_array((string)$path, $configModel->getEncryptedNodeEntriesPaths());
    }
}
