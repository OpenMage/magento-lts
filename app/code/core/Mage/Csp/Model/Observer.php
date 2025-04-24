<?php
class Mage_Csp_Model_Observer
{
    
    /**
     * Add Content Security Policy headers to the response
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function addCspHeaders(Varien_Event_Observer $observer)
    {
         /**
         * @var Mage_Csp_Helper_Data $helper
         */
        $helper = Mage::helper('csp');
        if (!$helper->isEnabled()) {
            return;
        }

        $response = $observer->getEvent()->getResponse();
        if ($response->canSendHeaders(true)) {
            $area = Mage::app()->getStore()->isAdmin() ? 'admin' : 'system';
            $directives = $helper->getPolicies( $area );
            $cspHeader = [];
            foreach ($directives as $directive => $value) {
                $cspHeader[] = $directive . ' ' . implode(' ', $value);
            }
            $header = $helper->getReportOnly() ? Mage_Csp_Helper_Data::HEADER_CONTENT_SECURITY_POLICY_REPORT_ONLY : Mage_Csp_Helper_Data::HEADER_CONTENT_SECURITY_POLICY;
            $response->setHeader($header, implode('; ', $cspHeader));
        }
    }
}
