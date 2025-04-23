<?php

declare(strict_types=1);

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Csp
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Csp_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_moduleName = 'Mage_Csp';

    public const CONFIG_MAPPING = [
        'default-src',
        'script-src',
        'style-src',
        'img-src',
        'connect-src',
        'font-src',
        'frame-src',
        'object-src',
        'media-src',
        'form-action',
    ];

    /**
     * @return array<string, string>
     */
    public function getPolicies(string $section): array
    {
        $result = [];

        if (!$this->isCspEnabled($section)) {
            return $result;
        }

        foreach (self::CONFIG_MAPPING as $key) {
            $result[$key] = $this->getCspConfigByKey($section, $key);
        }
        return $result;
    }

    public function isCspEnabled(string $section): bool
    {
        return Mage::getStoreConfigFlag("$section/csp/enabled");
    }

    public function isCspReportOnly(string $section): bool
    {
        return Mage::getStoreConfigFlag("$section/csp/report_only");
    }

    public function getCspConfigByKey(string $section, string $key): string
    {
        return Mage::getStoreConfig("$section/csp/$key");
    }

    public function getCspHeader(string $section): string
    {
        return $this->isCspReportOnly($section) ?
            'Content-Security-Policy-Report-Only' : 'Content-Security-Policy';
    }
}
