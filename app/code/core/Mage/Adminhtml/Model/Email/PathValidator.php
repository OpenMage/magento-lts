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
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
