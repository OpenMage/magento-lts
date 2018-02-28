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
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Url rewrite helper
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Helper_Url_Rewrite extends Mage_Core_Helper_Abstract
{
    /**#@+
     * Validation error constants
     */
    const VERR_MANYSLASHES = 1; // Too many slashes in a row of request path, e.g. '///foo//'
    const VERR_ANCHOR = 2;      // Anchor is not supported in request path, e.g. 'foo#bar'
    /**#@-*/

    /**
     * Allowed request path length
     */
    const TARGET_PATH_ALLOWED_LENGTH = 255;

    /**
     * Core func to validate request path
     * If something is wrong with a path it throws localized error message and error code,
     * that can be checked to by wrapper func to alternate error message
     *
     * @throws Mage_Core_Exception
     * @param string $requestPath
     * @return bool
     */
    protected function _validateRequestPath($requestPath)
    {
        if (strlen($requestPath) > self::TARGET_PATH_ALLOWED_LENGTH) {
            throw new Mage_Core_Exception(
                $this->__('Request path length exceeds allowed %s symbols.', self::TARGET_PATH_ALLOWED_LENGTH)
            );
        }
        if (strpos($requestPath, '//') !== false) {
            throw new Mage_Core_Exception(
                $this->__('Two and more slashes together are not permitted in request path'),
                self::VERR_MANYSLASHES
            );
        }
        if (strpos($requestPath, '#') !== false) {
            throw new Mage_Core_Exception(
                $this->__('Anchor symbol (#) is not supported in request path'),
                self::VERR_ANCHOR
            );
        }
        return true;
    }

    /**
     * Validates request path
     * Either returns TRUE (success) or throws error (validation failed)
     *
     * @param string $requestPath
     * @return bool
     */
    public function validateRequestPath($requestPath)
    {
        $this->_validateRequestPath($requestPath);
        return true;
    }

    /**
     * Validates suffix for url rewrites to inform user about errors in it
     * Either returns TRUE (success) or throws error (validation failed)
     *
     * @throws Mage_Core_Exception|Exception
     * @param string $suffix
     * @return bool
     */
    public function validateSuffix($suffix)
    {
        try {
            $this->_validateRequestPath($suffix); // Suffix itself must be a valid request path
        } catch (Exception $e) {
            // Make message saying about suffix, not request path
            switch ($e->getCode()) {
                case self::VERR_MANYSLASHES:
                    throw new Mage_Core_Exception($this->__('Two and more slashes together are not permitted in url rewrite suffix'));
                case self::VERR_ANCHOR:
                    throw new Mage_Core_Exception($this->__('Anchor symbol (#) is not supported in url rewrite suffix'));
            }
            throw $e;
        }
        return true;
    }
}
