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
 * @copyright   Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

/**
 * Rewritten to vanilla JS — no Prototype.js dependency.
 */

function setLocation(url){
    window.location.href = encodeURI(url);
}

function confirmSetLocation(message, url){
    if( confirm(message) ) {
        setLocation(url);
    }
    return false;
}

function deleteConfirm(message, url) {
    confirmSetLocation(message, url);
}

function setElementDisable(element, disable){
    var el = typeof element === 'string' ? document.getElementById(element) : element;
    if(el){
        el.disabled = disable;
    }
}

function toggleParentVis(obj) {
    obj = (typeof obj === 'string' ? document.getElementById(obj) : obj).parentNode;
    if( obj.style.display == 'none' ) {
        obj.style.display = '';
    } else {
        obj.style.display = 'none';
    }
}

function toggleFieldsetVis(obj) {
    var id = obj;
    obj = document.getElementById(obj);
    if( obj.style.display == 'none' ) {
        obj.style.display = '';
    } else {
        obj.style.display = 'none';
    }
    var siblings = Array.prototype.slice.call(obj.parentNode.children);
    for (var i = 0; i < siblings.length; i++) {
        if (siblings[i].id != undefined
            && siblings[i].id == id
            && i > 0 && siblings[i-1].className == 'entry-edit-head')
        {
            if (siblings[i-1].style.display == 'none') {
                siblings[i-1].style.display = '';
            } else {
                siblings[i-1].style.display = 'none';
            }
        }
    }
}

function toggleVis(obj) {
    obj = document.getElementById(obj);
    if( obj.style.display == 'none' ) {
        obj.style.display = '';
    } else {
        obj.style.display = 'none';
    }
}

function imagePreview(element){
    var el = document.getElementById(element);
    if(el){
        var win = window.open('', 'preview', 'width=400,height=400,resizable=1,scrollbars=1');
        win.document.open();
        win.document.write('<body style="padding:0;margin:0"><img src="'+el.src+'" id="image_preview"/></body>');
        win.document.close();
        win.addEventListener('load', function(){
            var img = win.document.getElementById('image_preview');
            win.resizeTo(img.width+40, img.height+80);
        });
    }
}

function checkByProductPriceType(elem) {
    if (elem.id == 'price_type') {
        this.productPriceType = elem.value;
        return false;
    } else {
        if (elem.id == 'price' && this.productPriceType == 0) {
            return false;
        }
        return true;
    }
}

window.addEventListener('load', function() {
    var priceDefault = document.getElementById('price_default');
    if (priceDefault && priceDefault.checked) {
        document.getElementById('price').disabled = 'disabled';
    }
});

function toggleValueElements(checkbox, container, excludedElements, checked){
    if(container && checkbox){
        var ignoredElements = [checkbox];
        if (typeof excludedElements != 'undefined') {
            if (Object.prototype.toString.call(excludedElements) != '[object Array]') {
                excludedElements = [excludedElements];
            }
            for (var i = 0; i < excludedElements.length; i++) {
                ignoredElements.push(excludedElements[i]);
            }
        }
        if (typeof container === 'string') {
            container = document.getElementById(container);
        }
        var elems = container.querySelectorAll('select, input, textarea, button, img');
        var isDisabled = (checked != undefined ? checked : checkbox.checked);
        elems.forEach(function (elem) {
            if (checkByProductPriceType(elem)) {
                var ignored = false;
                for (var j = 0; j < ignoredElements.length; j++) {
                    if (elem === ignoredElements[j]) { ignored = true; break; }
                }
                if (ignored) return;

                elem.disabled = isDisabled;
                if (isDisabled) {
                    elem.classList.add('disabled');
                } else {
                    elem.classList.remove('disabled');
                }
                if (elem.nodeName.toLowerCase() == 'img') {
                    elem.style.display = isDisabled ? 'none' : '';
                }
            }
        });
    }
}

function submitAndReloadArea(area, url) {
    var areaEl = typeof area === 'string' ? document.getElementById(area) : area;
    if(areaEl) {
        var fields = areaEl.querySelectorAll('input, select, textarea');
        var params = new URLSearchParams(new FormData());
        fields.forEach(function(field) {
            if (field.name) params.append(field.name, field.value);
        });
        if (!params.has('form_key') && window.FORM_KEY) {
            params.append('form_key', window.FORM_KEY);
        }
        url = url + (url.indexOf('?') !== -1 ? '&isAjax=true' : '?isAjax=true');
        showLoader();
        fetch(url, {
            method: 'POST',
            body: params,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(resp) { return resp.text(); })
        .then(function(text) {
            try {
                var response = JSON.parse(text);
                if (response.error) {
                    alert(response.message);
                }
                if(response.ajaxExpired && response.ajaxRedirect) {
                    setLocation(response.ajaxRedirect);
                }
            } catch (e) {
                areaEl.innerHTML = text;
                Array.from(areaEl.querySelectorAll('script')).forEach(function(oldScript) {
                    var newScript = document.createElement('script');
                    newScript.textContent = oldScript.textContent;
                    document.head.appendChild(newScript);
                    document.head.removeChild(newScript);
                });
            }
        })
        .finally(function() {
            hideLoader();
        });
    }
}

function syncOnchangeValue(baseElem, distElem){
    var compare = {baseElem:baseElem, distElem:distElem};
    (typeof baseElem === 'string' ? document.getElementById(baseElem) : baseElem)
        .addEventListener('change', function(){
            var base = typeof compare.baseElem === 'string' ? document.getElementById(compare.baseElem) : compare.baseElem;
            var dist = typeof compare.distElem === 'string' ? document.getElementById(compare.distElem) : compare.distElem;
            if(base && dist){
                dist.value = base.value;
            }
        });
}

function updateElementAtCursor(el, value, win) {
    if (win == undefined) {
        win = window.self;
    }
    if (document.selection) {
        el.focus();
        sel = win.document.selection.createRange();
        sel.text = value;
    } else if (el.selectionStart || el.selectionStart == '0') {
        var startPos = el.selectionStart;
        var endPos = el.selectionEnd;
        el.value = el.value.substring(0, startPos) + value + el.value.substring(endPos, el.value.length);
    } else {
        el.value += value;
    }
}

function firebugEnabled() {
    if(window.console && window.console.firebug) {
        return true;
    }
    return false;
}

function disableElement(elem) {
    elem.disabled = true;
    elem.classList.add('disabled');
}

function enableElement(elem) {
    elem.disabled = false;
    elem.classList.remove('disabled');
}

function disableElements(search){
    document.querySelectorAll('.' + search).forEach(disableElement);
}

function enableElements(search){
    document.querySelectorAll('.' + search).forEach(enableElement);
}

/********** Toolbar toggle **********/
var toolbarToggle = {
    header: null,
    headerOffset: null,
    headerCopy: null,
    eventsAdded: false,

    reset: function () {
        if (this.headerCopy) {
            this.headerCopy.remove();
        }
        this.createToolbar();
        this.updateForScroll();
    },

    createToolbar: function () {
        var headers = document.querySelectorAll('.content-header');
        this.header = null;
        for (var i = headers.length - 1; i >= 0; i--) {
            if (!headers[i].classList.contains('skip-header')) {
                this.header = headers[i];
                break;
            }
        }
        if (!this.header) {
            return;
        }

        this.headerOffset = this._cumulativeOffsetTop(this.header);

        var buttons = document.querySelector('.content-buttons');
        if (buttons) {
            var placeholder = document.createElement('div');
            placeholder.className = 'content-buttons-placeholder';
            buttons.parentNode.insertBefore(placeholder, buttons);
            buttons.placeholder = placeholder;
            buttons.remove();
            placeholder.appendChild(buttons);
            this.headerOffset = this._cumulativeOffsetTop(buttons);
        }

        this.headerCopy = document.createElement('div');
        this.headerCopy.appendChild(this.header.cloneNode(true));
        document.body.insertBefore(this.headerCopy, document.body.lastChild);
        this.headerCopy.classList.add('content-header-floating');

        var placeholderCopy = this.headerCopy.querySelector('.content-buttons-placeholder');
        if (placeholderCopy) {
            placeholderCopy.remove();
        }
    },

    _cumulativeOffsetTop: function(el) {
        var top = 0;
        while (el) {
            top += el.offsetTop || 0;
            el = el.offsetParent;
        }
        return top;
    },

    ready: function () {
        return (this.header && this.headerCopy && this.headerCopy.parentNode) ? true : false;
    },

    updateForScroll: function () {
        if (!this.ready()) {
            return;
        }
        var s = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
        if (s > this.headerOffset) {
            this.showFloatingToolbar();
        } else {
            this.showNormalToolbar();
        }
    },

    showNormalToolbar: function () {
        if (!this.ready()) return;
        var buttons = document.querySelector('.content-buttons');
        if (buttons && buttons.oldParent && buttons.oldParent != buttons.parentNode) {
            buttons.remove();
            if(buttons.oldBefore) {
                buttons.oldParent.insertBefore(buttons, buttons.oldBefore);
            } else {
                buttons.oldParent.appendChild(buttons);
            }
        }
        this.headerCopy.style.display = 'none';
    },

    showFloatingToolbar: function () {
        if (!this.ready()) return;
        var buttons = document.querySelector('.content-buttons');
        if (buttons) {
            if (!buttons.oldParent) {
                buttons.oldParent = buttons.parentNode;
                buttons.oldBefore = buttons.previousElementSibling;
            }
            if (buttons.oldParent == buttons.parentNode) {
                if (buttons.placeholder) {
                    buttons.placeholder.style.width = buttons.placeholder.offsetWidth + 'px';
                    buttons.placeholder.style.height = buttons.placeholder.offsetHeight + 'px';
                }
                var target = this.headerCopy.querySelector('div');
                if (target) {
                    buttons.style.display = 'none';
                    buttons.remove();
                    target.appendChild(buttons);
                    buttons.style.display = '';
                }
            }
        }
        this.headerCopy.style.display = 'block';
    },

    startOnLoad: function () {
        if (!this.funcOnWindowLoad) {
            this.funcOnWindowLoad = this.start.bind(this);
        }
        window.addEventListener('load', this.funcOnWindowLoad);
    },

    removeOnLoad: function () {
        if (!this.funcOnWindowLoad) return;
        window.removeEventListener('load', this.funcOnWindowLoad);
    },

    start: function () {
        this.reset();
        this.startListening();
    },

    stop: function () {
        this.stopListening();
        this.removeOnLoad();
        this.showNormalToolbar();
    },

    startListening: function () {
        if (this.eventsAdded) return;
        if (!this.funcUpdateForViewport) {
            this.funcUpdateForViewport = this.updateForScroll.bind(this);
        }
        window.addEventListener('scroll', this.funcUpdateForViewport);
        window.addEventListener('resize', this.funcUpdateForViewport);
        this.eventsAdded = true;
    },

    stopListening: function () {
        if (!this.eventsAdded) return;
        window.removeEventListener('scroll', this.funcUpdateForViewport);
        window.removeEventListener('resize', this.funcUpdateForViewport);
        this.eventsAdded = false;
    }
};

/** @deprecated */
function updateTopButtonToolbarToggle() { toolbarToggle.reset(); }
/** @deprecated */
function createTopButtonToolbarToggle() { toolbarToggle.createToolbar(); }
/** @deprecated */
function floatingTopButtonToolbarToggle() { toolbarToggle.updateForScroll(); }

toolbarToggle.startOnLoad();

/** Cookie Reading And Writing **/
var Cookie = {
    all: function() {
        var pairs = document.cookie.split(';');
        var cookies = {};
        pairs.forEach(function(item) {
            var pair = item.trim().split('=');
            cookies[unescape(pair[0])] = unescape(pair[1]);
        });
        return cookies;
    },
    read: function(cookieName) {
        var cookies = this.all();
        if(cookies[cookieName]) {
            return cookies[cookieName];
        }
        return null;
    },
    write: function(cookieName, cookieValue, cookieLifeTime) {
        var expires = '';
        if (cookieLifeTime) {
            var date = new Date();
            date.setTime(date.getTime()+(cookieLifeTime*1000));
            expires = '; expires='+date.toUTCString();
        }
        var urlPath = '/' + BASE_URL.split('/').slice(3).join('/');
        document.cookie = escape(cookieName) + "=" + escape(cookieValue) + expires + "; path=" + urlPath;
    },
    clear: function(cookieName) {
        this.write(cookieName, '', -1);
    }
};

var Fieldset = {
    cookiePrefix: 'fh-',
    applyCollapse: function(containerId) {
        var stateEl = document.getElementById(containerId + '-state');
        var headEl = document.getElementById(containerId + '-head');
        var bodyEl = document.getElementById(containerId);
        var collapsed;
        if (stateEl) {
            collapsed = stateEl.value == 1 ? 0 : 1;
        } else {
            collapsed = headEl.collapsed;
        }
        if (collapsed==1 || collapsed===undefined) {
            headEl.classList.remove('open');
            var sectionConfig = headEl.closest('.section-config');
            if(sectionConfig) {
                sectionConfig.classList.remove('active');
            }
            bodyEl.style.display = 'none';
        } else {
            headEl.classList.add('open');
            var sectionConfig = headEl.closest('.section-config');
            if(sectionConfig) {
                sectionConfig.classList.add('active');
            }
            bodyEl.style.display = '';
        }
    },
    toggleCollapse: function(containerId, saveThroughAjax) {
        var stateEl = document.getElementById(containerId + '-state');
        var headEl = document.getElementById(containerId + '-head');
        var collapsed;
        if (stateEl) {
            collapsed = stateEl.value == 1 ? 0 : 1;
        } else {
            collapsed = headEl.collapsed;
        }
        if(collapsed==1 || collapsed===undefined) {
            if (stateEl) {
                stateEl.value = 1;
            }
            headEl.collapsed = 0;
        } else {
            if (stateEl) {
                stateEl.value = 0;
            }
            headEl.collapsed = 1;
        }

        this.applyCollapse(containerId);
        if (typeof saveThroughAjax != "undefined") {
            this.saveState(saveThroughAjax, {container: containerId, value: document.getElementById(containerId + '-state').value});
        }
    },
    addToPrefix: function (value) {
        this.cookiePrefix += value + '-';
    },
    saveState: function(url, parameters) {
        fetch(url + '?' + new URLSearchParams(parameters).toString(), {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
    }
};

var Base64 = {
    _keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
    encode: function (input) {
        var output = "";
        var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
        var i = 0;
        input = Base64._utf8_encode(input);
        while (i < input.length) {
            chr1 = input.charCodeAt(i++);
            chr2 = input.charCodeAt(i++);
            chr3 = input.charCodeAt(i++);
            enc1 = chr1 >> 2;
            enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
            enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
            enc4 = chr3 & 63;
            if (isNaN(chr2)) {
                enc3 = enc4 = 64;
            } else if (isNaN(chr3)) {
                enc4 = 64;
            }
            output = output +
            this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
            this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);
        }
        return output;
    },
    decode: function (input) {
        var output = "";
        var chr1, chr2, chr3;
        var enc1, enc2, enc3, enc4;
        var i = 0;
        input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
        while (i < input.length) {
            enc1 = this._keyStr.indexOf(input.charAt(i++));
            enc2 = this._keyStr.indexOf(input.charAt(i++));
            enc3 = this._keyStr.indexOf(input.charAt(i++));
            enc4 = this._keyStr.indexOf(input.charAt(i++));
            chr1 = (enc1 << 2) | (enc2 >> 4);
            chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
            chr3 = ((enc3 & 3) << 6) | enc4;
            output = output + String.fromCharCode(chr1);
            if (enc3 != 64) {
                output = output + String.fromCharCode(chr2);
            }
            if (enc4 != 64) {
                output = output + String.fromCharCode(chr3);
            }
        }
        output = Base64._utf8_decode(output);
        return output;
    },
    mageEncode: function(input){
        return this.encode(input).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, ',');
    },
    mageDecode: function(output){
        output = output.replace(/\-/g, '+').replace(/_/g, '/').replace(/,/g, '=');
        return this.decode(output);
    },
    idEncode: function(input){
        return this.encode(input).replace(/\+/g, ':').replace(/\//g, '_').replace(/=/g, '-');
    },
    idDecode: function(output){
        output = output.replace(/\-/g, '=').replace(/_/g, '/').replace(/\:/g, '\+');
        return this.decode(output);
    },
    _utf8_encode: function (string) {
        string = string.replace(/\r\n/g,"\n");
        var utftext = "";
        for (var n = 0; n < string.length; n++) {
            var c = string.charCodeAt(n);
            if (c < 128) {
                utftext += String.fromCharCode(c);
            } else if((c > 127) && (c < 2048)) {
                utftext += String.fromCharCode((c >> 6) | 192);
                utftext += String.fromCharCode((c & 63) | 128);
            } else {
                utftext += String.fromCharCode((c >> 12) | 224);
                utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                utftext += String.fromCharCode((c & 63) | 128);
            }
        }
        return utftext;
    },
    _utf8_decode: function (utftext) {
        var string = "";
        var i = 0;
        var c = 0, c1 = 0, c2 = 0, c3 = 0;
        while ( i < utftext.length ) {
            c = utftext.charCodeAt(i);
            if (c < 128) {
                string += String.fromCharCode(c);
                i++;
            } else if((c > 191) && (c < 224)) {
                c2 = utftext.charCodeAt(i+1);
                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                i += 2;
            } else {
                c2 = utftext.charCodeAt(i+1);
                c3 = utftext.charCodeAt(i+2);
                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                i += 3;
            }
        }
        return string;
    }
};

function sortNumeric(val1, val2) {
    return val1 - val2;
}

function addCopyIcons() {
    if (navigator.clipboard === undefined) {
        return;
    }
    const copyTexts = document.querySelectorAll('[data-copy-text]');
    copyTexts.forEach(copyText => {
        const iconElement = createCopyIconElement();
        copyText.parentNode.appendChild(iconElement);
    });
}

function createCopyIconElement() {
    const copyIcon = document.createElement('span');
    copyIcon.classList.add('icon-copy');
    copyIcon.setAttribute('onclick', 'copyText(event)');
    copyIcon.setAttribute('title', Translator.translate('Copy text to clipboard'));
    return copyIcon;
}

function copyText(event) {
    event.stopPropagation();
    event.preventDefault();
    const copyIcon = event.currentTarget;
    const copyText = copyIcon.previousElementSibling.getAttribute('data-copy-text');
    navigator.clipboard.writeText(copyText);
    copyIcon.classList.add('icon-copy-copied');
    setTimeout(() => {
        copyIcon.classList.remove('icon-copy-copied');
    }, 1000);
}
