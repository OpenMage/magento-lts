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

class Mage_Csp_Model_Observer
{
    /**
     * Add Content Security Policy headers to the frontend response
     */
    public function addFrontendCspHeaders(Varien_Event_Observer $observer): void
    {
        $this->_addCspHeaders($observer, Mage_Core_Model_App_Area::AREA_FRONTEND);
    }

    /**
     * Add Content Security Policy headers to the admin response
     */
    public function addAdminCspHeaders(Varien_Event_Observer $observer): void
    {
        $this->_addCspHeaders($observer, Mage_Core_Model_App_Area::AREA_ADMINHTML);
    }

    /**
     * Common method to add CSP headers for a specific area
     *
     * @param Mage_Core_Model_App_Area::AREA_FRONTEND|Mage_Core_Model_App_Area::AREA_ADMINHTML $area
     */
    private function _addCspHeaders(Varien_Event_Observer $observer, string $area): void
    {
        /**
         * @var Mage_Core_Controller_Response_Http $response
         */
        $response = $observer->getEvent()->getResponse();
        if (!$response || !$response->canSendHeaders(true)) {
            return;
        }

        /**
         * @var Mage_Csp_Helper_Data $helper
         */
        $helper = Mage::helper('csp');
        if (!$helper->isEnabled($area)) {
            return;
        }
        $directives = $helper->getPolicies($area);
        if (empty($directives)) {
            return;
        }
        
        // Set the CSP Reporting-Endpoints header
        $reportUriEndpoint = null;
        if (!empty($helper->getReportUri($area))) {
            $reportUriEndpoint = trim($helper->getReportUri($area));
            $response->setHeader(Mage_Csp_Helper_Data::HEADER_CONTENT_SECURITY_POLICY_REPORT_URI, sprintf('csp-endpoint="%s"', $reportUriEndpoint));
        }

        $cspDirectives = $this->_compactHeaders($directives);
        // Check if the CSP directives should be split into multiple headers
        $shouldSplitHeaders = $helper->shouldSplitHeaders($area);
        if ($shouldSplitHeaders !== true) {
            $headerValue = implode('; ', $cspDirectives);
            $cspDirectives = [$headerValue];
        }
        // Set the CSP headers
        $headerName = $helper->getReportOnly($area)
            ? Mage_Csp_Helper_Data::HEADER_CONTENT_SECURITY_POLICY_REPORT_ONLY
            : Mage_Csp_Helper_Data::HEADER_CONTENT_SECURITY_POLICY;
        foreach ($cspDirectives as $headerValue) {
            if ($reportUriEndpoint !== null) {
                $headerValue .= '; report-uri ' . $reportUriEndpoint;
                $headerValue .= '; report-to csp-endpoint';
            }
            $response->setHeader($headerName, $headerValue);
        }
    }

    /**
     * Compact the CSP directives into a single string for each directive
     */
    private function _compactHeaders(array $directives): array
    {
        $cspParts = [];
        foreach ($directives as $directive => $values) {
            if (!empty($values)) {
                $cspParts[$directive] = $directive . ' ' . implode(' ', $values);
            }
        }
        return $cspParts;
    }
}
