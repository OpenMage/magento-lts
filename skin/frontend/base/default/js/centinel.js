/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    design
 * @package     base_default
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
var CentinelValidate = Class.create();
CentinelValidate.prototype = {
    initialize: function(iframe, payment, lookupUrl){
        this.iframe = iframe;
        this.payment = payment;
        this.lookupUrl = lookupUrl;
        if (payment) {
            this.paymentForm = payment.form;
            this.paymentSaveUrl = payment.saveUrl;
        } else {
            this.paymentForm = null;
            this.paymentSaveUrl = null;
        }
        
        this.nextStepContent = null;
    }, 
    
    /**
     * Send data to save it in quote, get next step content
     * @return
     */
    paymentProcess: function()
    {
        try {
            checkout.setLoadWaiting('payment');
        } catch(e) {}
        var params = Form.serialize(this.paymentForm);
        new Ajax.Request(this.paymentSaveUrl, {
            parameters: params,
            method: 'post',
            evalScript: true,
            onComplete: function(transport) {
                payment.nextStepContent = transport;
                this.centinelLookUp();
            }.bind(this)
        });
    },
    
    /**
     * Send request to centiel Api, get iframe proper src url.
     * 
     */
    centinelLookUp: function(payForm)
    {
        if (payForm) {
            this.paymentForm = payForm; 
        }
        var params = Form.serialize(this.paymentForm);
        new Ajax.Request(this.lookupUrl, {
            parameters: params,
            method: 'post',
            evalScript: true,
            onComplete: function(transport) {
                var response = transport.responseText.evalJSON();
                if (response.iframeUrl) {
                    $(this.iframe).src = response.iframeUrl;
                    $(this.iframe).show();
                    try {
                        checkout.setLoadWaiting(false);
                    } catch (e) {}
                } else {
                    this.nextStep();
                }
            }.bind(this)
        });
     },

     /**
      * Load payment next step
      * @return
      */
     nextStep: function()
     {
         if (this.payment) {
             this.payment.nextStep(payment.nextStepContent);
         } else {
             if (this.paymentForm) {
                 this.paymentForm.submit();
             }
         }
     },
     
     /**
      * Process success Iframe action, close iframe, load payment next step
      * 
      */
     processSuccessIframe: function (){
         $(this.iframe).hide();
         this.nextStep();
     },

     /**
      * Fail process iframe, close iframe alert 3d secure validation error
      * 
      * @param errorMsg string
      */
     processFailIframe: function processFailIframe(errorMsg) {
         $(this.iframe).hide();
         alert(errorMsg);
     }
}
