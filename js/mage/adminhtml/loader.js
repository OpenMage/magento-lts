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

/**
 * Rewritten to vanilla JS — no Prototype.js dependency.
 * If Ajax.Request exists (from shim or full Prototype), it is monkey-patched
 * to inject form_key and handle session expiry for backward compatibility.
 */

// -------------------------------------------------------------------------
// SessionError
// -------------------------------------------------------------------------
function SessionError(errorText) {
    this.errorText = errorText;
}
SessionError.prototype.toString = function () {
    return 'Session Error:' + this.errorText;
};

// -------------------------------------------------------------------------
// Helper: append isAjax and form_key to a URL / params
// -------------------------------------------------------------------------
function _openMageAjaxUrl(url) {
    if (!url.match(/[?&]isAjax=true/)) {
        url += (url.indexOf('?') !== -1 ? '&' : '?') + 'isAjax=true';
    }
    return url;
}

function _openMageInjectFormKey(params) {
    if (typeof params === 'string') {
        if (params.indexOf('form_key=') === -1) {
            params += '&form_key=' + encodeURIComponent(window.FORM_KEY);
        }
        return params;
    }
    if (!params) {
        params = {};
    }
    if (!params.form_key) {
        params.form_key = window.FORM_KEY;
    }
    return params;
}

function _openMageCheckSession(text) {
    try {
        var json = JSON.parse(text);
        if (json.ajaxExpired && json.ajaxRedirect) {
            window.location.replace(json.ajaxRedirect);
            return true;
        }
    } catch (e) {
        // not JSON — that's fine
    }
    return false;
}

// -------------------------------------------------------------------------
// Monkey-patch Ajax.Request / Ajax.Updater if they exist (shim or full)
// -------------------------------------------------------------------------
(function () {
    if (typeof Ajax === 'undefined' || typeof Ajax.Request === 'undefined') {
        return;
    }

    // Only patch in full Prototype.js — shim also provides addMethods but does not inject $super
    // when there is no parent class. Ajax.getTransport exists only in full Prototype (shim is fetch-based).
    if (typeof Ajax.getTransport === 'function') {
        Ajax.Request.addMethods({
            initialize: function ($super, url, options) {
                $super(options);
                this.transport = Ajax.getTransport();
                url = _openMageAjaxUrl(url);
                this.options.parameters = _openMageInjectFormKey(this.options.parameters);
                this.request(url);
            },
            respondToReadyState: function (readyState) {
                var state = Ajax.Request.Events[readyState],
                    response = new Ajax.Response(this);

                if (state == 'Complete') {
                    try {
                        this._complete = true;
                        if (_openMageCheckSession(response.responseText)) {
                            throw new SessionError('session expired');
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
                    this.transport.onreadystatechange = Prototype.emptyFunction;
                }
            }
        });
        Ajax.Updater.respondToReadyState = Ajax.Request.respondToReadyState;
    } else {
        // Shim mode: Ajax.Request is fetch-based, patch via options interceptor
        var _OrigRequest = Ajax.Request;
        var _OrigInit = _OrigRequest.prototype.initialize;
        _OrigRequest.prototype.initialize = function (url, options) {
            url = _openMageAjaxUrl(url);
            options = options || {};
            options.parameters = _openMageInjectFormKey(options.parameters);

            // Wrap onSuccess to check for session expiry
            var origSuccess = options.onSuccess;
            options.onSuccess = function (response) {
                if (_openMageCheckSession(response.responseText)) {
                    return;
                }
                if (typeof origSuccess === 'function') {
                    origSuccess(response);
                }
            };
            _OrigInit.call(this, url, options);
        };
    }
})();

// -------------------------------------------------------------------------
// varienLoader — AJAX loader with optional caching
// -------------------------------------------------------------------------
function varienLoader(caching) {
    this.callback = false;
    this.cache = {};
    this.caching = caching || false;
    this.url = false;
}

varienLoader.prototype = {
    getCache: function (url) {
        return this.cache[url] || false;
    },

    load: function (url, params, callback) {
        this.url = url;
        this.callback = callback;

        if (this.caching) {
            var cached = this.getCache(url);
            if (cached) {
                this.processResult(cached);
                return;
            }
        }

        if (typeof params.updaterId !== 'undefined') {
            new varienUpdater(params.updaterId, url, {
                evalScripts: true,
                onComplete: this.processResult.bind(this),
                onFailure: this._processFailure.bind(this)
            });
        } else if (typeof Ajax !== 'undefined' && typeof Ajax.Request !== 'undefined') {
            new Ajax.Request(url, {
                method: 'post',
                parameters: params || {},
                onComplete: this.processResult.bind(this),
                onFailure: this._processFailure.bind(this)
            });
        } else {
            // Vanilla fetch fallback (none mode)
            var self = this;
            var formData = new FormData();
            if (params) {
                Object.keys(params).forEach(function (key) {
                    formData.append(key, params[key]);
                });
            }
            if (!formData.has('form_key') && window.FORM_KEY) {
                formData.append('form_key', window.FORM_KEY);
            }
            fetch(_openMageAjaxUrl(url), { method: 'POST', body: formData })
                .then(function (resp) { return resp.text(); })
                .then(function (text) {
                    if (!_openMageCheckSession(text)) {
                        self.processResult({ responseText: text });
                    }
                })
                .catch(function () {
                    self._processFailure();
                });
        }
    },

    _processFailure: function () {
        location.href = window.BASE_URL;
    },

    processResult: function (transport) {
        if (this.caching) {
            this.cache[this.url] = transport;
        }
        if (this.callback) {
            this.callback(transport.responseText);
        }
    }
};

// -------------------------------------------------------------------------
// varienUpdater — AJAX updater with session expiry check
// -------------------------------------------------------------------------
window.varienUpdater = function (containerId, url, options) {
    var container = document.getElementById(containerId);
    options = options || {};
    var loaderArea = options.loaderArea;
    var method = options.method || 'POST';
    var parameters = _openMageInjectFormKey(options.parameters);
    var requestUrl = _openMageAjaxUrl(url);
    var fetchOptions = {
        method: method,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    };

    if (method.toUpperCase() === 'GET') {
        requestUrl += '&' + new URLSearchParams(parameters).toString();
    } else {
        fetchOptions.headers['Content-Type'] = 'application/x-www-form-urlencoded';
        fetchOptions.body = new URLSearchParams(parameters).toString();
    }

    showLoader(loaderArea);
    fetch(requestUrl, fetchOptions)
    .then(function (resp) { return resp.text(); })
    .then(function (text) {
        if (_openMageCheckSession(text)) {
            hideLoader();
            return;
        }
        if (container) {
            container.innerHTML = text;
            // innerHTML does not execute <script> tags; re-create them so
            // inline init scripts in the AJAX response run (unless evalScripts:false)
            if (options.evalScripts !== false) {
                container.querySelectorAll('script').forEach(function (old) {
                    var s = document.createElement('script');
                    if (old.src) { s.src = old.src; } else { s.textContent = old.textContent; }
                    old.parentNode.replaceChild(s, old);
                });
            }
        }
        if (typeof options.onComplete === 'function') {
            options.onComplete({ responseText: text });
        }
        hideLoader();
    })
    .catch(function () {
        hideLoader();
        if (typeof options.onFailure === 'function') {
            options.onFailure();
        }
    });
};

// -------------------------------------------------------------------------
// Loading mask — show/hide global loader
// -------------------------------------------------------------------------
if (!window.varienLoaderHandler) {
    var varienLoaderHandler = {};
}

varienLoaderHandler.handler = {
    onCreate: function (request) {
        if (request && request.options && request.options.loaderArea === false) {
            return;
        }
        showLoader();
    },
    onComplete: function (request) {
        if (!request || typeof Ajax === 'undefined' || Ajax.activeRequestCount === 0) {
            hideLoader();
        }
    }
};

var loaderTimeout = null;

function showLoader(loaderArea) {
    if (typeof loaderArea === 'string') {
        loaderArea = document.getElementById(loaderArea);
    }
    if (!loaderArea) {
        loaderArea = document.querySelector('#html-body .wrapper');
    }
    var loadingMask = document.getElementById('loading-mask');
    if (!loadingMask) {
        return;
    }
    if (loadingMask.style.display !== 'none' && loadingMask.offsetParent !== null) {
        return;
    }

    // Position the mask over the loader area
    if (loaderArea) {
        var rect = loaderArea.getBoundingClientRect();
        loadingMask.style.position = 'absolute';
        loadingMask.style.top = (window.scrollY + rect.top) + 'px';
        loadingMask.style.left = (rect.left - 2) + 'px';
        loadingMask.style.width = rect.width + 'px';
        loadingMask.style.height = rect.height + 'px';
    }

    loadingMask.style.display = '';
    var children = Array.prototype.slice.call(loadingMask.children);
    children.forEach(function (child) { child.style.display = 'none'; });

    loaderTimeout = setTimeout(function () {
        children.forEach(function (child) { child.style.display = ''; });
    }, typeof window.LOADING_TIMEOUT === 'undefined' ? 200 : window.LOADING_TIMEOUT);
}

function hideLoader() {
    var loadingMask = document.getElementById('loading-mask');
    if (loadingMask) {
        loadingMask.style.display = 'none';
    }
    if (loaderTimeout) {
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

// Register loader handler with Ajax.Responders if available
if (typeof Ajax !== 'undefined' && typeof Ajax.Responders !== 'undefined') {
    Ajax.Responders.register(varienLoaderHandler.handler);
}
