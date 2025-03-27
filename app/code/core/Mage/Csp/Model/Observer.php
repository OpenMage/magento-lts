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
class Mage_Csp_Model_Observer
{
    public function addCspHeaders(Varien_Event_Observer $observer): void
    {
        if (headers_sent() || Mage::app()->getStore()->isAdmin()) {
            return;
        }

        if (!Mage::getStoreConfigFlag('system/csp/enable_on_front')) {
            return;
        }

        $directives = [
            "default-src" => "'self'",
            "script-src"  => "'self' 'unsafe-inline' 'unsafe-eval' www.google-analytics.com www.googletagmanager.com stats.g.doubleclick.net www.paypal.com www.paypalobjects.com js.stripe.com connect.facebook.net",
            "style-src"   => "'self' 'unsafe-inline' fonts.googleapis.com maxcdn.bootstrapcdn.com",
            "img-src"     => "'self' data: googletagmanager.com www.google-analytics.com stats.g.doubleclick.net www.paypal.com www.paypalobjects.com connect.facebook.net",
            "connect-src" => "'self' www.google-analytics.com www.paypal.com securepayments.paypal.com api.braintreegateway.com js.stripe.com api.stripe.com",
            "font-src"    => "'self' data: fonts.gstatic.com maxcdn.bootstrapcdn.com",
            "frame-src"   => "'self' www.paypal.com payments.amazon.com",
            "object-src"  => "'none'",
            "media-src"   => "'self'",
            "form-action" => "'self' www.paypal.com securepayments.paypal.com"
        ];

        $policies = Mage::getSingleton('csp/config')->getPolicies();

        foreach ($policies as $directive => $hosts) {
            foreach ($hosts as $host) {
                $directives[$directive] .= ' ' . $host;
            }
        }
        $cspHeader = [];
        foreach ($directives as $directive => $value) {
            $cspHeader[] = $directive . " " . $value;
        }

        if (!Mage::getStoreConfigFlag('system/csp/report_only')) {
            header('Content-Security-Policy: ' . implode("; ", $cspHeader));
        } else {
            header('Content-Security-Policy-Report-Only: ' . implode("; ", $cspHeader));
        }
    }
}
