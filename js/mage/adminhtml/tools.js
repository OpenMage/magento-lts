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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
function setLocation(url){
    window.location.href = url;
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
            win.resizeTo(img.width+40, img.height+80)
        });
    }
}

function toggleValueElements(checkbox, container){
    if(container && checkbox){
        //var elems = container.select('select', 'input');
        var elems = Element.select(container, ['select', 'input', 'textarea', 'button', 'img']);
        elems.each(function (elem) {
            if(elem!=checkbox) {
                elem.disabled=checkbox.checked;
                if (checkbox.checked) {
                    elem.addClassName('disabled');
                } else {
                    elem.removeClassName('disabled');
                }
                if(elem.tagName == 'IMG') {
                    checkbox.checked ? elem.hide() : elem.show();
                }
            };
        })
    }
}

/**
 * @todo add validation for fields
 */
function submitAndReloadArea(area, url) {
    if($(area)) {
        var fields = $(area).select('input', 'select', 'textarea');
        var data = Form.serializeElements(fields, true);
        new Ajax.Request(url, {
            parameters: $H(data),
            loaderArea: area,
            onSuccess: function(transport) {
                try {
                    if (transport.responseText.isJSON()) {
                        var response = transport.responseText.evalJSON()
                        if (response.error) {
                            alert(response.message);
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
    var compare = {baseElem:baseElem, distElem:distElem}
    Event.observe(baseElem, 'change', function(){
        if($(this.baseElem) && $(this.distElem)){
            $(this.distElem).value = $(this.baseElem).value;
        }
    }.bind(compare));
}

/********** Ajax session expiration ***********/


if (!navigator.appVersion.match('MSIE 6.')) {
    var header, header_offset, header_copy;

    Event.observe(window, 'load', function() {
        var headers = $$('.content-header');
        for(var i=0; i<headers.length;i++) {
            if(!headers[i].hasClassName('skip-header')) {
                header = headers[i];
            }
        }

        if (!header) {
;            return
        }
        header_offset = Element.cumulativeOffset(header)[1];
        var buttons = $$('.content-buttons')[0];
        if (buttons) {
            Element.insert(buttons, {before: '<div class="content-buttons-placeholder"></div>'});
            buttons.placeholder = buttons.previous('.content-buttons-placeholder');
            buttons.remove();
            buttons.placeholder.appendChild(buttons);

            header_offset = Element.cumulativeOffset(buttons)[1];

        }

        header_copy = document.createElement('div');
        header_copy.appendChild(header.cloneNode(true));
        document.body.appendChild(header_copy);
        $(header_copy).addClassName('content-header-floating');
        if ($(header_copy).down('.content-buttons-placeholder')) {
            $(header_copy).down('.content-buttons-placeholder').remove();
        }
    });

    function floatingTopButtonToolbarToggle() {

        if (!header || !header_copy || !header_copy.parentNode) {
            return;
        }
        var s;
        // scrolling offset calculation via www.quirksmode.org
        if (self.pageYOffset){
            s = self.pageYOffset;
        }else if (document.documentElement && document.documentElement.scrollTop) {
            s = document.documentElement.scrollTop;
        }else if (document.body) {
            s = document.body.scrollTop;
        }


        var buttons = $$('.content-buttons')[0];

        if (s > header_offset) {
            if (buttons) {
                if (!buttons.oldParent) {
                    buttons.oldParent = buttons.parentNode;
                    buttons.oldBefore = buttons.previous();
                }
                if (buttons.oldParent==buttons.parentNode) {
                    var dimensions = buttons.placeholder.getDimensions() // Make static dimens.
                    buttons.placeholder.style.width = dimensions.width + 'px';
                    buttons.placeholder.style.height = dimensions.height + 'px';

                    buttons.hide();
                    buttons.remove();
                    $(header_copy).down('div').appendChild(buttons);
                    buttons.show();
                }
            }

            //header.style.visibility = 'hidden';
            header_copy.style.display = 'block';
        } else {
            if (buttons && buttons.oldParent && buttons.oldParent != buttons.parentNode) {
                buttons.remove();
                buttons.oldParent.insertBefore(buttons, buttons.oldBefore);
                //buttons.placeholder.style.width = undefined;
                //buttons.placeholder.style.height = undefined;
            }
            header.style.visibility = 'visible';
            header_copy.style.display = 'none';

        }
    }

    Event.observe(window, 'scroll', floatingTopButtonToolbarToggle);
    Event.observe(window, 'resize', floatingTopButtonToolbarToggle);
}

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
            expires = '; expires='+date.toGMTString();
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
           $(containerId).hide();
        } else {
           $(containerId + '-head').addClassName('open');
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