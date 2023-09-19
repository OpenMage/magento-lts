/**
 * OpenMage
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available at https://opensource.org/license/afl-3-0-php
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright   Copyright (c) 2022-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
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

Ajax.Request.addMethods({
    initialize: function($super, url, options){
        $super(options);
        this.transport = Ajax.getTransport();
        if (!url.match(new RegExp('[?&]isAjax=true',''))) {
            url = url.match(new RegExp('\\?',"g")) ? url + '&isAjax=true' : url + '?isAjax=true';
        }
        if (Object.isString(this.options.parameters)
            && this.options.parameters.indexOf('form_key=') == -1
        ) {
            this.options.parameters += '&' + Object.toQueryString({
                form_key: FORM_KEY
            });
        } else {
            if (!this.options.parameters) {
                this.options.parameters = {
                    form_key: FORM_KEY
                };
            }
            if (!this.options.parameters.form_key) {
                this.options.parameters.form_key = FORM_KEY;
            }
        }

        this.request(url);
    },
    respondToReadyState: function(readyState) {
        var state = Ajax.Request.Events[readyState], response = new Ajax.Response(this);

        if (state == 'Complete') {
            try {
                this._complete = true;
                if (response.responseText.isJSON()) {
                    var jsonObject = response.responseText.evalJSON();
                    if (jsonObject.ajaxExpired && jsonObject.ajaxRedirect) {
                        window.location.replace(jsonObject.ajaxRedirect);
                        throw new SessionError('session expired');
                    }
                }

                (this.options['on' + response.status]
                 || this.options['on' + (this.success() ? 'Success' : 'Failure')]
                 || Prototype.emptyFunction)(response, response.headerJSON);
            } catch (e) {
                this.dispatchException(e);
                if (e instanceof SessionError) {
                    return;
                }
            }

            var contentType = response.getHeader('Content-type');
            if (this.options.evalJS == 'force'
                || (this.options.evalJS && this.isSameOrigin() && contentType
                && contentType.match(/^\s*(text|application)\/(x-)?(java|ecma)script(;.*)?\s*$/i))) {
                this.evalResponse();
            }
        }

        try {
            (this.options['on' + state] || Prototype.emptyFunction)(response, response.headerJSON);
            Ajax.Responders.dispatch('on' + state, this, response, response.headerJSON);
        } catch (e) {
            this.dispatchException(e);
        }

        if (state == 'Complete') {
            // avoid memory leak in MSIE: clean up
            this.transport.onreadystatechange = Prototype.emptyFunction;
        }
    }
});

Ajax.Updater.respondToReadyState = Ajax.Request.respondToReadyState;
//Ajax.Updater = Object.extend(Ajax.Updater, {
//  initialize: function($super, container, url, options) {
//    this.container = {
//      success: (container.success || container),
//      failure: (container.failure || (container.success ? null : container))
//    };
//
//    options = Object.clone(options);
//    var onComplete = options.onComplete;
//    options.onComplete = (function(response, json) {
//      this.updateContent(response.responseText);
//      if (Object.isFunction(onComplete)) onComplete(response, json);
//    }).bind(this);
//
//    $super((url.match(new RegExp('\\?',"g")) ? url + '&isAjax=1' : url + '?isAjax=1'), options);
//  }
//});

var varienLoader = new Class.create();

varienLoader.prototype = {
    initialize : function(caching){
        this.callback= false;
        this.cache   = $H();
        this.caching = caching || false;
        this.url     = false;
    },

    getCache : function(url){
        if(this.cache.get(url)){
            return this.cache.get(url);
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
            new varienUpdater(params.updaterId, url, {
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
            this.cache.set(this.url, transport);
        }
        if(this.callback){
            this.callback(transport.responseText);
        }
    }
};

if (!window.varienLoaderHandler)
    var varienLoaderHandler = new Object();

varienLoaderHandler.handler = {
    onCreate: function(request) {
        if(request.options.loaderArea===false){
            return;
        }
        showLoader();
    },
    onComplete: function(transport) {
        if(Ajax.activeRequestCount == 0) {
            hideLoader();
        }
    }
};

var loaderTimeout = null;

function showLoader(loaderArea) {
    if($(loaderArea) === undefined) {
        loaderArea = $$('#html-body .wrapper')[0]; // Blocks all page
    }
    var loadingMask = $('loading-mask');
    if(Element.visible(loadingMask)) {
        return;
    }
    Element.clonePosition(loadingMask, loaderArea, {offsetLeft:-2});
    Element.show(loadingMask);
    Element.childElements(loadingMask).invoke('hide');
    loaderTimeout = setTimeout(function() {
        Element.childElements(loadingMask).invoke('show');
    }, typeof window.LOADING_TIMEOUT === 'undefined' ? 200 : window.LOADING_TIMEOUT);
}

function hideLoader() {
    Element.hide('loading-mask');
    if(loaderTimeout) {
        clearTimeout(loaderTimeout);
        loaderTimeout = null;
    }
}

/** @deprecated since 20.0.19 */
function setLoaderPosition() {
}

/** @deprecated since 20.0.19 */
function toggleSelectsUnderBlock(block, flag) {
}

Ajax.Responders.register(varienLoaderHandler.handler);

var varienUpdater = Class.create(Ajax.Updater, {
    updateContent: function($super, responseText) {
        if (responseText.isJSON()) {
            var responseJSON = responseText.evalJSON();
            if (responseJSON.ajaxExpired && responseJSON.ajaxRedirect) {
                window.location.replace(responseJSON.ajaxRedirect);
            }
        } else {
            $super(responseText);
        }
    }
});
