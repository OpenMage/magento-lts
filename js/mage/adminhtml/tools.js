/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright   Copyright (c) 2019 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
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
    if($(element)){
        $(element).disabled = disable;
    }
}

function toggleParentVis(obj) {
    obj = $(obj).parentNode;
    if( obj.style.display == 'none' ) {
        obj.style.display = '';
    } else {
        obj.style.display = 'none';
    }
}

// to fix new app/design/adminhtml/default/default/template/widget/form/renderer/fieldset.phtml
// with toggleParentVis
function toggleFieldsetVis(obj) {
    id = obj;
    obj = $(obj);
    if( obj.style.display == 'none' ) {
        obj.style.display = '';
    } else {
        obj.style.display = 'none';
    }
    obj = obj.parentNode.childElements();
    for (var i = 0; i < obj.length; i++) {
        if (obj[i].id != undefined
            && obj[i].id == id
            && obj[(i-1)].classNames() == 'entry-edit-head')
        {
            if (obj[i-1].style.display == 'none') {
                obj[i-1].style.display = '';
            } else {
                obj[i-1].style.display = 'none';
            }
        }
    }
}

function toggleVis(obj) {
    obj = $(obj);
    if( obj.style.display == 'none' ) {
        obj.style.display = '';
    } else {
        obj.style.display = 'none';
    }
}

function imagePreview(element){
    if($(element)){
        var win = window.open('', 'preview', 'width=400,height=400,resizable=1,scrollbars=1');
        win.document.open();
        win.document.write('<body style="padding:0;margin:0"><img src="'+$(element).src+'" id="image_preview"/></body>');
        win.document.close();
        Event.observe(win, 'load', function(){
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

Event.observe(window, 'load', function() {
    if ($('price_default') && $('price_default').checked) {
        $('price').disabled = 'disabled';
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
        //var elems = container.select('select', 'input');
        var elems = Element.select(container, ['select', 'input', 'textarea', 'button', 'img']);
        var isDisabled = (checked != undefined ? checked : checkbox.checked);
        elems.each(function (elem) {
            if (checkByProductPriceType(elem)) {
                var i = ignoredElements.length;
                while (i-- && elem != ignoredElements[i]);
                if (i != -1) {
                    return;
                }

                elem.disabled = isDisabled;
                if (isDisabled) {
                    elem.addClassName('disabled');
                } else {
                    elem.removeClassName('disabled');
                }
                if (elem.nodeName.toLowerCase() == 'img') {
                    isDisabled ? elem.hide() : elem.show();
                }
            }
        });
    }
}

/**
 * @todo add validation for fields
 */
function submitAndReloadArea(area, url) {
    if($(area)) {
        var fields = $(area).select('input', 'select', 'textarea');
        var data = Form.serializeElements(fields, true);
        url = url + (url.match(new RegExp('\\?')) ? '&isAjax=true' : '?isAjax=true');
        new Ajax.Request(url, {
            parameters: $H(data),
            loaderArea: area,
            onSuccess: function(transport) {
                try {
                    if (transport.responseText.isJSON()) {
                        var response = transport.responseText.evalJSON();
                        if (response.error) {
                            alert(response.message);
                        }
                        if(response.ajaxExpired && response.ajaxRedirect) {
                            setLocation(response.ajaxRedirect);
                        }
                    } else {
                        $(area).update(transport.responseText);
                    }
                }
                catch (e) {
                    $(area).update(transport.responseText);
                }
            }
        });
    }
}

/********** MESSAGES ***********/
/*
Event.observe(window, 'load', function() {
    $$('.messages .error-msg').each(function(el) {
        new Effect.Highlight(el, {startcolor:'#E13422', endcolor:'#fdf9f8', duration:1});
    });
    $$('.messages .warning-msg').each(function(el) {
        new Effect.Highlight(el, {startcolor:'#E13422', endcolor:'#fdf9f8', duration:1});
    });
    $$('.messages .notice-msg').each(function(el) {
        new Effect.Highlight(el, {startcolor:'#E5B82C', endcolor:'#fbf7e9', duration:1});
    });
    $$('.messages .success-msg').each(function(el) {
        new Effect.Highlight(el, {startcolor:'#507477', endcolor:'#f2fafb', duration:1});
    });
});
*/
function syncOnchangeValue(baseElem, distElem){
    var compare = {baseElem:baseElem, distElem:distElem};
    Event.observe(baseElem, 'change', function(){
        if($(this.baseElem) && $(this.distElem)){
            $(this.distElem).value = $(this.baseElem).value;
        }
    }.bind(compare));
}

// Insert some content to the cursor position of input element
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

// Firebug detection
function firebugEnabled() {
    if(window.console && window.console.firebug) {
        return true;
    }
    return false;
}

function disableElement(elem) {
    elem.disabled = true;
    elem.addClassName('disabled');
}

function enableElement(elem) {
    elem.disabled = false;
    elem.removeClassName('disabled');
}

function disableElements(search){
    $$('.' + search).each(disableElement);
}

function enableElements(search){
    $$('.' + search).each(enableElement);
}

/********** Toolbar toggle object to manage normal/floating toolbar toggle during vertical scroll ***********/
var toolbarToggle = {
    // Properties
    header: null, // Normal toolbar
    headerOffset: null, // Normal toolbar offset - calculated once
    headerCopy: null, // Floating toolbar
    eventsAdded: false, // We're listening to scroll/resize
    compatible: !navigator.appVersion.match('MSIE 6.'), // Whether object is compatible with browser (do not support old browsers, legacy code)

    // Inits object and pushes it into work. Can be used to init/reset(update) object by current DOM.
    reset: function () {
        // Maybe we are already using floating toolbar - just remove it to update from html
        if (this.headerCopy) {
            this.headerCopy.remove();
        }
        this.createToolbar();
        this.updateForScroll();
    },

    // Creates toolbar and inits all needed properties
    createToolbar: function () {
        if (!this.compatible) {
            return;
        }

        // Extract header that we will use as toolbar
        var headers = $$('.content-header');
        for (var i = headers.length - 1; i >= 0; i--) {
            if (!headers[i].hasClassName('skip-header')) {
                this.header = headers[i];
                break;
            }
        }
        if (!this.header) {
            return;
        }

        // Calculate header offset once - for optimization
        this.headerOffset = Element.cumulativeOffset(this.header)[1];

        // Toolbar buttons
        var buttons = $$('.content-buttons')[0];
        if (buttons) {
            // Wrap buttons with 'placeholder' div - to serve as container for buttons
            Element.insert(buttons, {before: '<div class="content-buttons-placeholder"></div>'});
            buttons.placeholder = buttons.previous('.content-buttons-placeholder');
            buttons.remove();
            buttons.placeholder.appendChild(buttons);

            this.headerOffset = Element.cumulativeOffset(buttons)[1];
        }

        // Create copy of header, that will serve as floating toolbar docked to top of window
        this.headerCopy = $(document.createElement('div'));
        this.headerCopy.appendChild(this.header.cloneNode(true));
        document.body.insertBefore(this.headerCopy, document.body.lastChild);
        this.headerCopy.addClassName('content-header-floating');

        // Remove duplicated buttons and their container
        var placeholder = this.headerCopy.down('.content-buttons-placeholder');
        if (placeholder) {
            placeholder.remove();
        }
    },

    // Checks whether object properties are ready and valid
    ready: function () {
        // Return definitely boolean value
        return (this.compatible && this.header && this.headerCopy && this.headerCopy.parentNode) ? true : false;
    },

    // Updates toolbars for current scroll - shows/hides normal and floating toolbar
    updateForScroll: function () {
        if (!this.ready()) {
            return;
        }

        // scrolling offset calculation via www.quirksmode.org
        var s;
        if (self.pageYOffset){
            s = self.pageYOffset;
        } else if (document.documentElement && document.documentElement.scrollTop) {
            s = document.documentElement.scrollTop;
        } else if (document.body) {
            s = document.body.scrollTop;
        }

        // Show floating or normal toolbar
        if (s > this.headerOffset) {
            // Page offset is more than header offset, switch to floating toolbar
            this.showFloatingToolbar();
        } else {
            // Page offset is less than header offset, switch to normal toolbar
            this.showNormalToolbar();
        }
    },

    // Shows normal toolbar (and hides floating one)
    showNormalToolbar: function () {
        if (!this.ready()) {
            return;
        }

        var buttons = $$('.content-buttons')[0];
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

    // Shows floating toolbar (and hides normal one)
    // Notice that buttons could had changed in html by setting new inner html,
    // so our added custom properties (placeholder, oldParent) can be not present in them any more
    showFloatingToolbar: function () {
        if (!this.ready()) {
            return;
        }

        var buttons = $$('.content-buttons')[0];

        if (buttons) {
            // Remember original parent in normal toolbar to which these buttons belong
            if (!buttons.oldParent) {
                buttons.oldParent = buttons.parentNode;
                buttons.oldBefore = buttons.previous();
            }

            // Move buttons from normal to floating toolbar
            if (buttons.oldParent == buttons.parentNode) {
                // Make static dimensions for placeholder, so it's not collapsed when buttons are removed
                if (buttons.placeholder) {
                    var dimensions = buttons.placeholder.getDimensions();
                    buttons.placeholder.style.width = dimensions.width + 'px';
                    buttons.placeholder.style.height = dimensions.height + 'px';
                }

                // Move to floating
                var target = this.headerCopy.down('div');
                if (target) {
                    buttons.hide();
                    buttons.remove();

                    target.appendChild(buttons);
                    buttons.show();
                }
            }
        }

        this.headerCopy.style.display = 'block';
    },

    // Starts object on window load
    startOnLoad: function () {
        if (!this.compatible) {
            return;
        }

        if (!this.funcOnWindowLoad) {
            this.funcOnWindowLoad = this.start.bind(this);
        }
        Event.observe(window, 'load', this.funcOnWindowLoad);
    },

    // Removes object start on window load
    removeOnLoad: function () {
        if (!this.funcOnWindowLoad) {
            return;
        }
        Event.stopObserving(window, 'load', this.funcOnWindowLoad);
    },

    // Starts object by creating toolbar and enabling scroll/resize events
    start: function () {
        if (!this.compatible) {
            return;
        }

        this.reset();
        this.startListening();
    },

    // Stops object by removing toolbar and stopping listening to events
    stop: function () {
        this.stopListening();
        this.removeOnLoad();
        this.showNormalToolbar();
    },

    // Addes events on scroll/resize
    startListening: function () {
        if (this.eventsAdded) {
            return;
        }

        if (!this.funcUpdateForViewport) {
            this.funcUpdateForViewport = this.updateForScroll.bind(this);
        }

        Event.observe(window, 'scroll', this.funcUpdateForViewport);
        Event.observe(window, 'resize', this.funcUpdateForViewport);

        this.eventsAdded = true;
    },

    // Removes listening to events on resize/update
    stopListening: function () {
        if (!this.eventsAdded) {
            return;
        }
        Event.stopObserving(window, 'scroll', this.funcUpdateForViewport);
        Event.stopObserving(window, 'resize', this.funcUpdateForViewport);

        this.eventsAdded = false;
    }
};

// Deprecated since 1.4.2.0-beta1 - use toolbarToggle.reset() instead
function updateTopButtonToolbarToggle()
{
    toolbarToggle.reset();
}

// Deprecated since 1.4.2.0-beta1 - use toolbarToggle.createToolbar() instead
function createTopButtonToolbarToggle()
{
    toolbarToggle.createToolbar();
}

// Deprecated since 1.4.2.0-beta1 - use toolbarToggle.updateForScroll() instead
function floatingTopButtonToolbarToggle()
{
    toolbarToggle.updateForScroll();
}

// Start toolbar on window load
toolbarToggle.startOnLoad();


/** Cookie Reading And Writing **/

var Cookie = {
    all: function() {
        var pairs = document.cookie.split(';');
        var cookies = {};
        pairs.each(function(item, index) {
            var pair = item.strip().split('=');
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
        var urlPath = '/' + BASE_URL.split('/').slice(3).join('/'); // Get relative path
        document.cookie = escape(cookieName) + "=" + escape(cookieValue) + expires + "; path=" + urlPath;
    },
    clear: function(cookieName) {
        this.write(cookieName, '', -1);
    }
};

var Fieldset = {
    cookiePrefix: 'fh-',
    applyCollapse: function(containerId) {
        //var collapsed = Cookie.read(this.cookiePrefix + containerId);
        //if (collapsed !== null) {
        //    Cookie.clear(this.cookiePrefix + containerId);
        //}
        if ($(containerId + '-state')) {
            collapsed = $(containerId + '-state').value == 1 ? 0 : 1;
        } else {
            collapsed = $(containerId + '-head').collapsed;
        }
        if (collapsed==1 || collapsed===undefined) {
           $(containerId + '-head').removeClassName('open');
           if($(containerId + '-head').up('.section-config')) {
                $(containerId + '-head').up('.section-config').removeClassName('active');
           }
           $(containerId).hide();
        } else {
           $(containerId + '-head').addClassName('open');
           if($(containerId + '-head').up('.section-config')) {
                $(containerId + '-head').up('.section-config').addClassName('active');
           }
           $(containerId).show();
        }
    },
    toggleCollapse: function(containerId, saveThroughAjax) {
        if ($(containerId + '-state')) {
            collapsed = $(containerId + '-state').value == 1 ? 0 : 1;
        } else {
            collapsed = $(containerId + '-head').collapsed;
        }
        //Cookie.read(this.cookiePrefix + containerId);
        if(collapsed==1 || collapsed===undefined) {
            //Cookie.write(this.cookiePrefix + containerId,  0, 30*24*60*60);
            if ($(containerId + '-state')) {
                $(containerId + '-state').value = 1;
            }
            $(containerId + '-head').collapsed = 0;
        } else {
            //Cookie.clear(this.cookiePrefix + containerId);
            if ($(containerId + '-state')) {
                $(containerId + '-state').value = 0;
            }
            $(containerId + '-head').collapsed = 1;
        }

        this.applyCollapse(containerId);
        if (typeof saveThroughAjax != "undefined") {
            this.saveState(saveThroughAjax, {container: containerId, value: $(containerId + '-state').value});
        }
    },
    addToPrefix: function (value) {
        this.cookiePrefix += value + '-';
    },
    saveState: function(url, parameters) {
        new Ajax.Request(url, {
            method: 'get',
            parameters: Object.toQueryString(parameters),
            loaderArea: false
        });
    }
};

var Base64 = {
    // private property
    _keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
     //'+/=', '-_,'
    // public method for encoding
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

    // public method for decoding
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

    // private method for UTF-8 encoding
    _utf8_encode : function (string) {
        string = string.replace(/\r\n/g,"\n");
        var utftext = "";

        for (var n = 0; n < string.length; n++) {

            var c = string.charCodeAt(n);

            if (c < 128) {
                utftext += String.fromCharCode(c);
            }
            else if((c > 127) && (c < 2048)) {
                utftext += String.fromCharCode((c >> 6) | 192);
                utftext += String.fromCharCode((c & 63) | 128);
            }
            else {
                utftext += String.fromCharCode((c >> 12) | 224);
                utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                utftext += String.fromCharCode((c & 63) | 128);
            }
        }
        return utftext;
    },

    // private method for UTF-8 decoding
    _utf8_decode : function (utftext) {
        var string = "";
        var i = 0;
        var c = c1 = c2 = 0;

        while ( i < utftext.length ) {

            c = utftext.charCodeAt(i);

            if (c < 128) {
                string += String.fromCharCode(c);
                i++;
            }
            else if((c > 191) && (c < 224)) {
                c2 = utftext.charCodeAt(i+1);
                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                i += 2;
            }
            else {
                c2 = utftext.charCodeAt(i+1);
                c3 = utftext.charCodeAt(i+2);
                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                i += 3;
            }
        }
        return string;
    }
};

/**
 * Array functions
 */

/**
 * Callback function for sort numeric values
 *
 * @param val1
 * @param val2
 */
function sortNumeric(val1, val2)
{
    return val1 - val2;
}
