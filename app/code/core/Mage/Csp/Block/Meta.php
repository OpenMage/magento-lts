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

/**
 * CSP Meta Block
 *
 * @category   Mage
 * @package    Mage_Csp
 */
class Mage_Csp_Block_Meta extends Mage_Core_Block_Template
{
    /**
     * CSP directives
     */
    protected array $_directives = [];

    /**
     * CSP meta tag area
     */
    protected string $_area = Mage_Core_Model_App_Area::AREA_FRONTEND;

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

        if (!isset($this->_directives[$directive])) {
            $this->_directives[$directive] = [];
        }

        $this->_directives[$directive][] = $value;

        return $this;
    }

    /**
     * Get CSP policy content
     */
    public function getContents(): string
    {
        $content = [];
        foreach ($this->_directives as $directive => $values) {
            if (!empty($values)) {
                $content[] = $directive . ' ' . implode(' ', $values);
            }
        }
        $content = implode('; ', $content);
        return trim($content);
    }

    /**
     * Render CSP meta tag
     */
    protected function _toHtml(): string
    {
        if (empty($this->_directives)) {
            return '';
        }
        /**
         * @var Mage_Csp_Helper_Data $helper
         */
        $helper = Mage::helper('csp');
        if (!$helper->isEnabled($this->_area)) {
            return '';
        }
        $headerValue = $this->getContents();
        if (!empty($helper->getReportUri($this->_area))) {
            $reportUriEndpoint = trim($helper->getReportUri($this->_area));
            $headerValue .= '; report-uri ' . $reportUriEndpoint;
        }
        $headerName = $helper->getReportOnly($this->_area)
            ? $helper::HEADER_CONTENT_SECURITY_POLICY_REPORT_ONLY
            : $helper::HEADER_CONTENT_SECURITY_POLICY;

        return sprintf(
            '<meta http-equiv="%s" content="%s" />' . PHP_EOL,
            $headerName,
            $headerValue,
        );
    }
}
