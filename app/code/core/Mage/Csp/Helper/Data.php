<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Csp
 */

class Mage_Csp_Helper_Data extends Mage_Core_Helper_Abstract
{
    public const XML_CPS_ENABLED = 'csp/%s/enabled';

    public const XML_CSP_REPORT_ONLY = 'csp/%s/report_only';

    public const XML_CSP_REPORT_URI = 'csp/%s/report_uri';

    public const XML_CSP_SPLIT_HEADERS = 'csp/%s/split_headers';

    public const XML_CSP_MERGE_META = 'csp/%s/merge_meta';

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
    public function getReportUri(string $area): string
    {
        $config = Mage::getStoreConfig(sprintf(self::XML_CSP_REPORT_URI, $area));
        return is_string($config) ? $config : '';
    }

    /**
     * Check if CSP headers should be split into multiple headers for each directive
     * @param Mage_Core_Model_App_Area::AREA_FRONTEND|Mage_Core_Model_App_Area::AREA_ADMINHTML $area
     */
    public function shouldSplitHeaders(string $area): bool
    {
        return Mage::getStoreConfigFlag(sprintf(self::XML_CSP_SPLIT_HEADERS, $area));
    }

    /**
     * Check if CSP meta tags should be merged into a single header
     * @param Mage_Core_Model_App_Area::AREA_FRONTEND|Mage_Core_Model_App_Area::AREA_ADMINHTML $area
     */
    public function shouldMergeMeta(string $area): bool
    {
        return Mage::getStoreConfigFlag(sprintf(self::XML_CSP_MERGE_META, $area));
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
     * @return array<string, array<string>>
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

        /** @var array<string, array<string>> $policy */
        return $policy;
    }

    /**
     * Get global policy for a specific directive
     * global/csp/<directiveName>
     * @param value-of<self::CSP_DIRECTIVES> $directiveName
     * @return array<string>
     */
    public function getGlobalPolicy(string $directiveName = 'default-src'): array
    {
        $config = Mage::getConfig();
        if (!$config) {
            return [];
        }

        $globalNode = $config->getNode(sprintf('global/csp/%s', $directiveName));
        if ($globalNode) {
            /** @var array<string> $result */
            $result = $globalNode->asArray();
            return $result;
        }

        return [];
    }

    /**
     * Get area policy for a specific directive
     * (adminhtml|frontend)/csp/<directiveName>
     * @param Mage_Core_Model_App_Area::AREA_FRONTEND|Mage_Core_Model_App_Area::AREA_ADMINHTML $area
     * @param value-of<self::CSP_DIRECTIVES> $directiveName
     * @return array<string>
     */
    public function getAreaPolicy(string $area = Mage_Core_Model_App_Area::AREA_FRONTEND, string $directiveName = 'default-src'): array
    {
        $config = Mage::getConfig();
        if (!$config) {
            return [];
        }

        $areaNode = $config->getNode(sprintf('%s/csp/%s', $area, $directiveName));
        if ($areaNode) {
            /** @var array<string> $result */
            $result = $areaNode->asArray();
            return $result;
        }

        return [];
    }


    /**
     * Get system policy for a specific directive
     * csp/(adminhtml|frontend)/<directiveName>
     * @param Mage_Core_Model_App_Area::AREA_FRONTEND|Mage_Core_Model_App_Area::AREA_ADMINHTML $area
     * @param value-of<self::CSP_DIRECTIVES> $directiveName
     * @return array<string>
     */
    public function getStoreConfigPolicy(string $area = Mage_Core_Model_App_Area::AREA_FRONTEND, string $directiveName = 'default-src'): array
    {
        /** @var array<string>|null $systemNode */
        $systemNode = Mage::getStoreConfig(sprintf('csp/%s/%s', $area, $directiveName));
        if ($systemNode) {
            if (is_string($systemNode) && preg_match('/^a:\d+:{.*}$/', $systemNode)) {
                $unserializedData = unserialize($systemNode);
                if (is_array($unserializedData)) {
                    /** @var array<string> $systemNode */
                    $systemNode = array_column($unserializedData, 'host');
                }
            }

            return $systemNode;
        }

        return [];
    }
}
