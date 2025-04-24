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

    public const XML_CPS_ENABLED = 'csp/%s/enabled';
    public const XML_CSP_REPORT_ONLY = 'csp/%s/report_only';
    public const XML_CSP_REPORT_URI = 'csp/%s/report_uri';
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

    /**
     * Check if CSP is enabled
     * @param string $area
     * @return bool
     */
    public function isEnabled($area): bool
    {
        return Mage::getStoreConfigFlag(sprintf(self::XML_CPS_ENABLED, $area));
    }

    /**
     * Check if report only mode is enabled
     * @param string $area
     * @return bool
     */
    public function getReportOnly($area): bool
    {
        return Mage::getStoreConfigFlag( sprintf(self::XML_CSP_REPORT_ONLY, $area));
    }

    /**
     * Get report uri
     * @param string $area
     * @return string
     */
    public function getReportUri($area): string
    {
        return Mage::getStoreConfig( sprintf(self::XML_CSP_REPORT_URI, $area));
    }

    /**
     * Get the appropriate CSP header based on the area and report only mode
     * @param string $area
     * @return string
     */
    public function getReportOnlyHeader($area): string
    {
        return $this->getReportOnly($area) ? self::HEADER_CONTENT_SECURITY_POLICY_REPORT_ONLY : self::HEADER_CONTENT_SECURITY_POLICY;
    }

    /**
     * Get CSP policies for a specific area
     * @param string $area
     * @return array
     */
    public function getPolicies($area = Mage_Core_Model_App_Area::AREA_FRONTEND): array
    {
        if (!$this->isEnabled($area)) {
            return [];
        }
        $policy = [];
        foreach (self::CSP_DIRECTIVES as $directiveName) {
            $policy[$directiveName] = [];
            $policy[$directiveName] = array_merge_recursive(
                $this->getGlobalPolicy($directiveName),
                $this->getAreaPolicy($area, $directiveName),
                $this->getStoreConfigPolicy($area, $directiveName)
            );
            $policy[$directiveName] = array_unique($policy[$directiveName]);
        }

        return $policy;
    }

    /**
     * Get global policy for a specific directive
     * global/csp/<directiveName>
     * @param string $directiveName
     * @return array
     */
    public function getGlobalPolicy($directiveName = 'default-src'): array
    {
        $globalNode = Mage::getConfig()->getNode(sprintf("global/csp/%s", $directiveName));
        if ($globalNode) {
            return $globalNode->asArray();
        }
        return [];
    }

    /**
     * Get area policy for a specific directive
     * (adminhtml|frontend)/csp/<directiveName>
     * @param string $area
     * @param string $directiveName
     * @return array
     */
    public function getAreaPolicy($area = Mage_Core_Model_App_Area::AREA_FRONTEND, $directiveName = 'default-src'): array
    {
        $areaNode = Mage::getConfig()->getNode(sprintf('%s/csp/%s', $area, $directiveName));
        if ($areaNode) {
            return $areaNode->asArray();
        }
        return [];
    }


    /**
     * Get system policy for a specific directive
     * csp/(adminhtml|frontend)/<directiveName>
     * @param string $area
     * @param string $directiveName
     * @return array
     */
    public function getStoreConfigPolicy($area = Mage_Core_Model_App_Area::AREA_FRONTEND, $directiveName = 'default-src'): array
    {
        $systemNode = Mage::getStoreConfig(sprintf('csp/%s/%s', $area, $directiveName));
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
