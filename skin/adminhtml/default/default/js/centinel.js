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
 * @package     default_default
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
var CentinelValidate = Class.create();
CentinelValidate.prototype = {
    initialize: function(iframe, editForm, lookupUrl){
        this.iframe = iframe;
        this.closeButton = iframe + '_close_btn'; 
        this.payment = payment;
        this.lookupUrl = lookupUrl;
        this.paymentForm = editForm;
        this.nextStepContent = null;
    }, 
    
    /**
     * Send request to centiel Api, get iframe proper src url.
     * 
     */
    centinelLookUp: function(form)
    {
        this.paymentForm = form;
        var params = Form.serialize(this.paymentForm);
        params = params + '&admin_store=true&customer_id=' + order.customerId;
        new Ajax.Request(this.lookupUrl, {
            parameters: params,
            method: 'post',
            evalScript: true,
            onComplete: function(transport) {
                var response = transport.responseText.evalJSON();
                if (response.iframeUrl) {
                    try{
                        this.show();
                        $(this.iframe).src = response.iframeUrl;
                    } catch(e) {
                        this.nextStep();
                    }
                } else {
                    this.nextStep();
                }
            }.bind(this)
        });
     },

     setPaymentForm: function(form)
     {
         this.paymentForm = form;
         return true;
     },
     
     /**
      * Load payment next step
      * @return
      */
     nextStep: function()
     {
         $(this.paymentForm).submit();
     },
     
     /**
      * Process success Iframe action, close iframe, load payment next step
      * 
      */
     processSuccessIframe: function (){
    	 this.close();
         this.nextStep();
     },

     /**
      * Fail process iframe, close iframe alert 3d secure validation error
      * 
      * @param errorMsg string
      */
     processFailIframe: function processFailIframe(errorMsg) {
         this.close();
         alert(errorMsg);
     },
     
     /**
      * Close iframe
      * 
      */
     close: function()
     {
         $(this.iframe).hide();
         $(this.closeButton).hide();
     },
     
     /**
      * show iframe content
      * 
      */
     show: function()
     {
         $(this.iframe).show();
         $(this.closeButton).show();
     }     
}