<?php

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

class Mage_Csp_Block_Csp extends Mage_Core_Block_Abstract
{
    protected array $items = [];
    protected string $section = 'system';

    public function addItem(string $type, string $data): self
    {
        $this->items[$type] [] = $data;
        return $this;
    }

    /**
     * @throws Zend_Controller_Response_Exception
     */
    protected function _toHtml(): string
    {
        $response = $this->getAction()->getResponse();
        if (!$response->canSendHeaders()) {
            return '';
        }

        /**
         * @var Mage_Csp_Helper_Data $helper
         */
        $helper = Mage::helper('csp');

        if (!Mage::getStoreConfigFlag("$this->section/csp/enabled")) {
            return '';
        }
        /**
         * @var Mage_Csp_Model_Config $config
         */
        $config = Mage::getSingleton('csp/config');
        $directives = array_merge_recursive(
            $helper->getPolicies($this->section),
            $config->getPolicies(),
            $this->items,
        );
        $cspHeader = [];
        foreach ($directives as $directive => $value) {
            $cspHeader[] = $directive . ' ' . (is_array($value) ? implode(' ', $value) : (string) $value);
        }

        $header = Mage::getStoreConfigFlag("$this->section/csp/report_only") ?
            'Content-Security-Policy-Report-Only' : 'Content-Security-Policy';
        $response->setHeader($header, implode('; ', $cspHeader));
        return '';
    }
}
