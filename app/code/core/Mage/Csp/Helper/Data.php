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
 * @license    https://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */
class Mage_Csp_Helper_Data extends Mage_Core_Helper_Abstract
{

    public const XML_CPS_ENABLED = 'system/csp/enabled';
    public const XML_CSP_REPORT_ONLY = 'system/csp/report_only';
    public const HEADER_CONTENT_SECURITY_POLICY = 'Content-Security-Policy';
    public const HEADER_CONTENT_SECURITY_POLICY_REPORT_ONLY = 'Content-Security-Policy-Report-Only';
    public const CSP_DIRECTIVES = [
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

    public function isEnabled(): bool
    {
        return Mage::getStoreConfigFlag(self::XML_CPS_ENABLED);
    }

    public function getReportOnly(): bool
    {
        return Mage::getStoreConfigFlag(self::XML_CSP_REPORT_ONLY);
    }

    /**
     * Get CSP policies
     * @return array
     */
    public function getPolicies($area = 'frontend'): array
    {
        if (!$this->isEnabled()) {
            return [];
        }
        $policy = [];
        foreach (self::CSP_DIRECTIVES as $key) {
            $policy[$key] = [];

            $globalNode = Mage::getConfig()->getNode("global/csp/$key");
            $areaNode = Mage::getConfig()->getNode("$area/csp/$key");
            $systemNode = $this->_getSystemPolicy($area, $key);
            $policy[$key] = array_merge_recursive(
                $globalNode ? $globalNode->asArray() : [],
                $areaNode ? $areaNode->asArray() : [],
                $systemNode
            );

            $policy[$key] = array_unique($policy[$key]);
        }

        return $policy;
    }

    private function _getSystemPolicy($area = 'system', $key)
    {
        $systemNode = Mage::getStoreConfig("$area/csp/$key");
        if ($systemNode) {
            if (is_string($systemNode) && preg_match('/^a:\d+:{.*}$/', $systemNode)) {
                $unserializedData = unserialize($systemNode);
                $systemNode = array_column($unserializedData, 'host');
            }
            return $systemNode;
        }
        return [];
    }
}
