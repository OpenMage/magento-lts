/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

var SessionError = Class.create();
SessionError.prototype = {
    initialize: function(errorText) {
        this.errorText = errorText;
    },
    toString: function()
    {
        return 'Session Error:' + this.errorText;
    }
};


Ajax.Request.prototype = Object.extend(Ajax.Request.prototype, {
   initialize: function(url, options) {
    this.transport = Ajax.getTransport();
    this.setOptions(options);
    this.request((url.match(new RegExp('\\?',"g")) ? url + '&isAjax=1' : url + '?isAjax=1'));
  },
  respondToReadyState: function(readyState) {
    var state = Ajax.Request.Events[readyState];
    var transport = this.transport, json = this.evalJSON();

    if (state == 'Complete') {
      try {
        this._complete = true;
        if (transport.responseText.isJSON()) {
           var _checkData  = transport.responseText.evalJSON();
           if(typeof _checkData == 'object' && _checkData.ajaxExpired && _checkData.ajaxRedirect) {
               window.location.replace(_checkData.ajaxRedirect);
               throw new SessionError('session expired');
           }
        }
        (this.options['on' + this.transport.status]
         || this.options['on' + (this.success() ? 'Success' : 'Failure')]
         || Prototype.emptyFunction)(transport, json);
      } catch (e) {
        this.dispatchException(e);
        if (e instanceof SessionError) {
            return;
        }
      }

      var contentType = this.getHeader('Content-type');
      if (contentType && contentType.strip().
        match(/^(text|application)\/(x-)?(java|ecma)script(;.*)?$/i))
          this.evalResponse();
    }

    try {
      (this.options['on' + state] || Prototype.emptyFunction)(transport, json);
      Ajax.Responders.dispatch('on' + state, this, transport, json);
    } catch (e) {
      this.dispatchException(e);
    }

    if (state == 'Complete') {
      // avoid memory leak in MSIE: clean up
      this.transport.onreadystatechange = Prototype.emptyFunction;
    }
  }
});

Ajax.Updater.prototype.respondToReadyState = Ajax.Request.prototype.respondToReadyState;

Ajax.Updater.prototype = Object.extend(Ajax.Updater.prototype, {
    initialize: function(container, url, options) {
    this.container = {
      success: (container.success || container),
      failure: (container.failure || (container.success ? null : container))
    }

    this.transport = Ajax.getTransport();
    this.setOptions(options);

    var onComplete = this.options.onComplete || Prototype.emptyFunction;
    this.options.onComplete = (function(transport, param) {
      this.updateContent();
      onComplete(transport, param);
    }).bind(this);

    this.request((url.match(new RegExp('\\?',"g")) ? url + '&isAjax=1' : url + '?isAjax=1'));
  }
});

var varienLoader = new Class.create();

varienLoader.prototype = {
    initialize : function(caching){
        this.callback= false;
        this.cache   = $H();
        this.caching = caching || false;
        this.url     = false;
    },

    getCache : function(url){
        if(this.cache[url]){
            return this.cache[url]
        }
        return false;
    },

    load : function(url, params, callback){
        this.url      = url;
        this.callback = callback;

        if(this.caching){
            var transport = this.getCache(url);
            if(transport){
                this.processResult(transport);
                return;
            }
        }

        if (typeof(params.updaterId) != 'undefined') {
            new Ajax.Updater(params.updaterId, url, {
                evalScripts : true,
                onComplete: this.processResult.bind(this),
                onFailure: this._processFailure.bind(this)
            });
        }
        else {
            new Ajax.Request(url,{
                method: 'post',
                parameters: params || {},
                onComplete: this.processResult.bind(this),
                onFailure: this._processFailure.bind(this)
            });
        }
    },

    _processFailure : function(transport){
        location.href = BASE_URL;
    },

    processResult : function(transport){
        if(this.caching){
            this.cache[this.url] = transport;
        }
        if(this.callback){
            this.callback(transport.responseText);
        }
    }
}

if (!window.varienLoaderHandler)
    var varienLoaderHandler = new Object();

varienLoaderHandler.handler = {
    onCreate: function(request) {
        if(request.options.loaderArea===false){
            return;
        }

        request.options.loaderArea = $$('#html-body .wrapper')[0]; // Blocks all page

        if(request && request.options.loaderArea){
            Position.clone($(request.options.loaderArea), $('loading-mask'), {offsetLeft:-2});
            toggleSelectsUnderBlock($('loading-mask'), false);
            Element.show('loading-mask');
            setLoaderPosition();
            if(request.options.loaderArea=='html-body'){
                //Element.show('loading-process');
            }
        }
        else{
            //Element.show('loading-process');
        }
    },

    onComplete: function(transport) {
        if(Ajax.activeRequestCount == 0) {
            //Element.hide('loading-process');
            toggleSelectsUnderBlock($('loading-mask'), true);
            Element.hide('loading-mask');
        }
    }
};

/**
 * @todo need calculate middle of visible area and scroll bind
 */
function setLoaderPosition(){
    var elem = $('loading_mask_loader');
    if (elem && Prototype.Browser.IE) {
        var middle = parseInt(document.body.clientHeight/2)+document.body.scrollTop;
        elem.style.position = 'absolute';
        elem.style.top = middle;
    }
}

/*function getRealHeight() {
    var body = document.body;
    if (window.innerHeight && window.scrollMaxY) {
        return window.innerHeight + window.scrollMaxY;
    }
    return Math.max(body.scrollHeight, body.offsetHeight);
}*/



function toggleSelectsUnderBlock(block, flag){
    if(Prototype.Browser.IE){
        var selects = document.getElementsByTagName("select");
        for(var i=0; i<selects.length; i++){
            /**
             * @todo: need check intersection
             */
            if(flag){
                if(selects[i].needShowOnSuccess){
                    selects[i].needShowOnSuccess = false;
                    // Element.show(selects[i])
                    selects[i].style.visibility = '';
                }
            }
            else{
                if(Element.visible(selects[i])){
                    // Element.hide(selects[i]);
                    selects[i].style.visibility = 'hidden';
                    selects[i].needShowOnSuccess = true;
                }
            }
        }
    }
}

Ajax.Responders.register(varienLoaderHandler.handler);
