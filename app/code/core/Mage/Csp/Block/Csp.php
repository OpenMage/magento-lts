<?php
/**
 * OpenMage
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available at https://opensource.org/license/afl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Csp
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

class Mage_Csp_Block_Csp extends Mage_Core_Block_Abstract
{
    protected array $items = [];
    protected string $section = "system";

    public function addItem(string $type, string $data): self
    {
        $this->items[$type] []= $data;
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
         * @var $helper Mage_Csp_Helper_Data
         */
        $helper = Mage::helper('csp');

        if (!Mage::getStoreConfigFlag("$this->section/csp/enabled")) {
            return '';
        }
        /**
         * @var $config Mage_Csp_Model_Config
         */
        $config = Mage::getSingleton('csp/config');
        $directives = array_merge_recursive(
            $helper->getPolicies($this->section),
            $config->getPolicies(),
            $this->items
        );
        $cspHeader = [];
        foreach ($directives as $directive => $value) {
            $cspHeader[] = $directive . " " . (is_array($value) ? implode(" ", $value) : strval($value));
        }

        $header = Mage::getStoreConfigFlag("$this->section/csp/report_only") ?
            'Content-Security-Policy-Report-Only' : 'Content-Security-Policy';
        $response->setHeader($header, implode("; ", $cspHeader));
        return '';
    }
}