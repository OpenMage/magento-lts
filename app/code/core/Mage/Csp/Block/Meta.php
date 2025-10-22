<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Csp
 */

/**
 * CSP Meta Block
 *
 * @package Mage_Csp
 */
class Mage_Csp_Block_Meta extends Mage_Core_Block_Template
{
    /**
     * CSP directives
     * @var array<value-of<Mage_Csp_Helper_Data::CSP_DIRECTIVES>, array<string>>
     */
    protected array $directives = [];

    /**
     * CSP meta tag area
     * @var Mage_Core_Model_App_Area::AREA_FRONTEND|Mage_Core_Model_App_Area::AREA_ADMINHTML
     */
    protected string $area = Mage_Core_Model_App_Area::AREA_FRONTEND;

    /**
     * Add CSP directive
     *
     * @param value-of<Mage_Csp_Helper_Data::CSP_DIRECTIVES> $directive
     */
    public function addDirective(string $directive, string $value): static
    {
        if (!in_array($directive, Mage_Csp_Helper_Data::CSP_DIRECTIVES)) {
            return $this;
        }

        if (!isset($this->directives[$directive])) {
            $this->directives[$directive] = [];
        }

        $this->directives[$directive][] = $value;

        return $this;
    }

    /**
     * Get CSP directives
     * @return array<value-of<Mage_Csp_Helper_Data::CSP_DIRECTIVES>, array<string>>
     */
    public function getDirectives(): array
    {
        return $this->directives;
    }

    /**
     * Get CSP policy content
     */
    public function getContents(): string
    {
        $content = [];
        foreach ($this->directives as $directive => $values) {
            if (!empty($values)) {
                $content[] = $directive . ' ' . implode(' ', $values);
            }
        }

        $content = implode('; ', $content);
        return trim($content);
    }

    /**
     * Render CSP meta tag if enabled
     */
    protected function _toHtml(): string
    {
        if (empty($this->directives)) {
            return '';
        }

        /** @var Mage_Csp_Helper_Data $helper */
        $helper = Mage::helper('csp');
        if (!$helper->isEnabled($this->area) || $helper->shouldMergeMeta($this->area)) {
            return '';
        }

        $headerValue = $this->getContents();
        if (!empty($helper->getReportUri($this->area))) {
            $reportUriEndpoint = trim($helper->getReportUri($this->area));
            $headerValue .= '; report-uri ' . $reportUriEndpoint;
        }

        $headerName = $helper->getReportOnly($this->area)
            ? Mage_Csp_Helper_Data::HEADER_CONTENT_SECURITY_POLICY_REPORT_ONLY
            : Mage_Csp_Helper_Data::HEADER_CONTENT_SECURITY_POLICY;

        return sprintf(
            '<meta http-equiv="%s" content="%s" />' . PHP_EOL,
            $headerName,
            $headerValue,
        );
    }
}
