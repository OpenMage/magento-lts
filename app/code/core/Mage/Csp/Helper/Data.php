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
    public const HEADER_CONTENT_SECURITY_POLICY_REPORT_URI = 'Reporting-Endpoints';
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

    protected $_moduleName = 'Mage_Csp';


    /**
     * Check if CSP is enabled
     * @param Mage_Core_Model_App_Area::AREA_FRONTEND|Mage_Core_Model_App_Area::AREA_ADMINHTML $area
     */
    public function isEnabled(string $area): bool
    {
        return Mage::getStoreConfigFlag(sprintf(self::XML_CPS_ENABLED, $area));
    }

    /**
     * Check if report only mode is enabled
     * @param Mage_Core_Model_App_Area::AREA_FRONTEND|Mage_Core_Model_App_Area::AREA_ADMINHTML $area
     */
    public function getReportOnly(string $area): bool
    {
        return Mage::getStoreConfigFlag(sprintf(self::XML_CSP_REPORT_ONLY, $area));
    }

    /**
     * Get report uri
     * @param Mage_Core_Model_App_Area::AREA_FRONTEND|Mage_Core_Model_App_Area::AREA_ADMINHTML $area
     */
    public function getReportOnly(string $area): bool
    {
        return Mage::getStoreConfig(sprintf(self::XML_CSP_REPORT_URI, $area));
    }

    /**
     * Get the appropriate CSP header based on the area and report only mode
     * @param Mage_Core_Model_App_Area::AREA_FRONTEND|Mage_Core_Model_App_Area::AREA_ADMINHTML $area
     */
    public function getReportOnlyHeader(string $area): string
    {
        return $this->getReportOnly($area) ? self::HEADER_CONTENT_SECURITY_POLICY_REPORT_ONLY : self::HEADER_CONTENT_SECURITY_POLICY;
    }

    /**
     * Get CSP policies for a specific area
     * @param Mage_Core_Model_App_Area::AREA_FRONTEND|Mage_Core_Model_App_Area::AREA_ADMINHTML $area
     */
    public function getPolicies(string $area = Mage_Core_Model_App_Area::AREA_FRONTEND): array
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
                $this->getStoreConfigPolicy($area, $directiveName),
            );
            $policy[$directiveName] = array_unique($policy[$directiveName]);
        }

        return $policy;
    }

    /**
     * Get global policy for a specific directive
     * global/csp/<directiveName>
     * @param value-of<self::CSP_DIRECTIVES> $directiveName
     */
    public function getGlobalPolicy(string $directiveName = 'default-src'): array
    {
        $globalNode = Mage::getConfig()->getNode(sprintf('global/csp/%s', $directiveName));
        if ($globalNode) {
            return $globalNode->asArray();
        }
        return [];
    }

    /**
     * Get area policy for a specific directive
     * (adminhtml|frontend)/csp/<directiveName>
     * @param Mage_Core_Model_App_Area::AREA_FRONTEND|Mage_Core_Model_App_Area::AREA_ADMINHTML $area
     * @param value-of<self::CSP_DIRECTIVES> $directiveName
     */
    public function getAreaPolicy(string $area = Mage_Core_Model_App_Area::AREA_FRONTEND, string $directiveName = 'default-src'): array
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
     * @param Mage_Core_Model_App_Area::AREA_FRONTEND|Mage_Core_Model_App_Area::AREA_ADMINHTML $area
     * @param value-of<self::CSP_DIRECTIVES> $directiveName
     */
    public function getStoreConfigPolicy(string $area = Mage_Core_Model_App_Area::AREA_FRONTEND, string $directiveName = 'default-src'): array
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
