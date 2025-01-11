<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Url rewrite helper
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Helper_Url_Rewrite extends Mage_Core_Helper_Abstract
{
    /**
     * Validation error constants
     */
    public const VERR_MANYSLASHES = 1; // Too many slashes in a row of request path, e.g. '///foo//'
    public const VERR_ANCHOR = 2;      // Anchor is not supported in request path, e.g. 'foo#bar'

    /**
     * Allowed request path length
     */
    public const TARGET_PATH_ALLOWED_LENGTH = 255;

    protected $_moduleName = 'Mage_Core';

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
                $this->__('Request path length exceeds allowed %s symbols.', self::TARGET_PATH_ALLOWED_LENGTH),
            );
        }
        if (str_contains($requestPath, '//')) {
            throw new Mage_Core_Exception(
                $this->__('Two and more slashes together are not permitted in request path'),
                self::VERR_MANYSLASHES,
            );
        }
        if (strpos($requestPath, '#') !== false) {
            throw new Mage_Core_Exception(
                $this->__('Anchor symbol (#) is not supported in request path'),
                self::VERR_ANCHOR,
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
