<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Csp
 */

abstract class Mage_Csp_Model_Observer_Abstract
{
    /**
     * Common method to add CSP headers for a specific area
     *
     * @param Mage_Core_Model_App_Area::AREA_FRONTEND|Mage_Core_Model_App_Area::AREA_ADMINHTML $area
     * @throws Zend_Controller_Response_Exception
     */
    protected function addCspHeaders(Varien_Event_Observer $observer, string $area): void
    {
        /** @var Mage_Core_Controller_Response_Http $response */
        $response = $observer->getEvent()->getDataByKey('response');
        if (!$response || !$response->canSendHeaders(true)) {
            return;
        }

        /** @var Mage_Csp_Helper_Data $helper */
        $helper = Mage::helper('csp');
        if (!$helper->isEnabled($area)) {
            return;
        }

        $directives = $helper->getPolicies($area);
        if (empty($directives)) {
            return;
        }

        // Merge meta directives if needed
        if ($helper->shouldMergeMeta($area)) {
            $blockCspMeta = Mage::app()->getLayout()->getBlock('csp_meta');
            if ($blockCspMeta instanceof Mage_Csp_Block_Meta) {
                $metaDirectives = $blockCspMeta->getDirectives();
                foreach ($metaDirectives as $directive => $values) {
                    $directives[$directive] = array_unique(
                        array_merge($directives[$directive] ?? [], $values),
                    );
                }
            }
        }

        // Set the CSP Reporting-Endpoints header
        $reportUriEndpoint = null;
        if (!empty($helper->getReportUri($area))) {
            $reportUriEndpoint = trim($helper->getReportUri($area));
            $response->setHeader(
                Mage_Csp_Helper_Data::HEADER_CONTENT_SECURITY_POLICY_REPORT_URI,
                sprintf('csp-endpoint="%s"', $reportUriEndpoint),
            );
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
     * @param array<string, array<string>> $directives
     * @return array<string>
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
