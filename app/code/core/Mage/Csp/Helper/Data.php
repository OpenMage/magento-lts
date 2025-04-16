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
class Mage_Csp_Helper_Data extends Mage_Core_Helper_Abstract
{
    public const CONFIG_MAPPING = [
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


    public function getPolicies(string $section): array
    {
        if (!Mage::getStoreConfigFlag("$section/csp/enabled")) {
            return [];
        }
        $result = [];
        foreach (self::CONFIG_MAPPING as $key) {
            $result [$key] = Mage::getStoreConfig("$section/csp/$key");
        }
        return $result;
    }
}
