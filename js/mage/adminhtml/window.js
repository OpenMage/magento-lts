/**
 * OpenMage
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available at https://opensource.org/license/afl-3-0-php
 *
 * @category    Mage
 * @package     js
 * @copyright   Copyright (c) 2006 Sébastien Gruhier (http://xilinus.com, http://itseb.com)
 * @copyright   Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright   Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

/**
 * Vanilla JS rewrite of the prototype-windows library (window.js v1.3).
 * Preserves the original HTML structure so existing magento.css styles apply.
 * Removes all Prototype.js / Scriptaculous dependencies.
 */

/* -----------------------------------------------------------------------
 * Windows namespace
 * --------------------------------------------------------------------- */
var Windows = {
    windows:      [],
    modalWindows: [],
    focusedWindow: null,
    maxZIndex:    0,
    overlayShowEffectOptions: { duration: 0 },
    overlayHideEffectOptions: { duration: 0 },

    register: function (win) {
        this.windows.push(win);
    },

    unregister: function (win) {
        this.windows = this.windows.filter(function (w) { return w !== win; });
    },

    getWindow: function (id) {
        return this.windows.find(function (w) { return w.getId() === id; }) || null;
    },

    focus: function (id) {
        var win = this.getWindow(id);
        if (win) win.toFront();
    },

    close: function (id, event) {
        var win = this.getWindow(id);
        if (win) win.close();
        if (event && event.stopPropagation) { event.stopPropagation(); event.preventDefault(); }
    },

    minimize: function (id, event) {
        var win = this.getWindow(id);
        if (win && win.visible) win.minimize();
        if (event && event.stopPropagation) { event.stopPropagation(); event.preventDefault(); }
    },

    maximize: function (id, event) {
        var win = this.getWindow(id);
        if (win && win.visible) win.maximize();
        if (event && event.stopPropagation) { event.stopPropagation(); event.preventDefault(); }
    },

    updateZindex: function (zindex, win) {
        if (zindex > this.maxZIndex) {
            this.maxZIndex = zindex;
        }
        this.focusedWindow = win;
    },

    addModalWindow: function (win) {
        if (this.modalWindows.length === 0) {
            // Overlay gets current maxZIndex+1, then window will be set above it
            _WindowUtilities.disableScreen(win.options.className, this.maxZIndex + 1);
            this.maxZIndex++;
        }
        this.modalWindows.push(win);
    },

    removeModalWindow: function (win) {
        this.modalWindows = this.modalWindows.filter(function (w) { return w !== win; });
        if (this.modalWindows.length === 0) {
            _WindowUtilities.enableScreen();
        }
    }
};

/* -----------------------------------------------------------------------
 * Internal utilities (overlay management)
 * --------------------------------------------------------------------- */
var _WindowUtilities = {
    _overlay: null,

    disableScreen: function (className, zIndex) {
        if (this._overlay) return;
        var el = document.createElement('div');
        el.id        = 'overlay_modal';
        el.className = 'overlay_' + className;
        el.style.cssText = 'display:block;position:fixed;top:0;left:0;width:100%;height:100%;z-index:' + zIndex;
        document.body.appendChild(el);
        this._overlay = el;
    },

    enableScreen: function () {
        if (this._overlay && this._overlay.parentNode) {
            this._overlay.parentNode.removeChild(this._overlay);
        }
        this._overlay = null;
    }
};

/* -----------------------------------------------------------------------
 * Window constructor
 * --------------------------------------------------------------------- */
function Window(options) {
    // Legacy: new Window("id", {…})
    if (typeof options === 'string') {
        var legacyId = options;
        options = arguments[1] || {};
        options.id = legacyId;
    }
    options = options || {};

    if (!options.id) {
        options.id = 'window_' + Date.now();
    }

    this.options = Object.assign({
        className:       'dialog',
        windowClassName: null,
        title:           '&nbsp;',
        width:           200,
        height:          300,
        top:             null,
        left:            null,
        zIndex:          1000,
        resizable:       true,
        closable:        true,
        minimizable:     true,
        maximizable:     true,
        draggable:       true,
        showEffect:      null,
        hideEffect:      null,
        recenterAuto:    false,
        destroyOnClose:  false,
        closeOnEsc:      true,
        onClose:         null,
        closeCallback:   null
    }, options);

    this.visible         = false;
    this.minimized       = false;
    this.modal           = false;
    this._storedLocation = null;

    this.element = this._createWindow(this.options.id);
    this.element.win = this;

    this.topbar    = document.getElementById(this.options.id + '_top');
    this.bottombar = document.getElementById(this.options.id + '_bottom');
    this.content   = document.getElementById(this.options.id + '_content');

    if (this.options.draggable) {
        this._initDragging();
    }

    if (this.options.closeOnEsc) {
        var self = this;
        document.addEventListener('keyup', function (e) {
            if (e.keyCode === 27 && self.visible) self.close();
        });
    }

    this.setTitle(this.options.title);
    if (this.options.width && this.options.height) {
        this.setSize(this.options.width, this.options.height);
    }

    Windows.register(this);
}

Window.prototype = {
    getId: function () {
        return this.element.id;
    },

    getContent: function () {
        return this.content;
    },

    setDestroyOnClose: function () {
        this.options.destroyOnClose = true;
    },

    setTitle: function (title) {
        var el = document.getElementById(this.getId() + '_top');
        if (el) el.innerHTML = title || '&nbsp;';
    },

    setSize: function (width, height) {
        this.width  = parseFloat(width);
        this.height = parseFloat(height);
        this.element.style.width = this.width + 'px';
        if (this.content) {
            this.content.style.width = this.width + 'px';
            this.content.style.height = this.height + 'px';
        }
    },

    setZIndex: function (z) {
        this.element.style.zIndex = z;
        Windows.updateZindex(z, this);
    },

    toFront: function () {
        this.setZIndex(Windows.maxZIndex + 1);
    },

    showCenter: function (modal, top, left) {
        this.visible = true;

        var scrollY = window.scrollY || document.documentElement.scrollTop  || 0;
        var scrollX = window.scrollX || document.documentElement.scrollLeft || 0;
        var vw      = window.innerWidth  || document.documentElement.clientWidth;
        var vh      = window.innerHeight || document.documentElement.clientHeight;

        var t = (top  !== undefined && top  !== null)
            ? top  + scrollY
            : scrollY + Math.max(0, (vh - (this.height || 300)) / 2);
        var l = (left !== undefined && left !== null)
            ? left + scrollX
            : scrollX + Math.max(0, (vw - (this.width  || 200)) / 2);

        this.element.style.top  = t + 'px';
        this.element.style.left = l + 'px';
        this.element.style.display = '';

        if (modal) {
            // Overlay is created first (lower z-index), then window is placed above it
            Windows.addModalWindow(this);
            this.modal = true;
        }

        this.setZIndex(Windows.maxZIndex + 1);

        if (typeof this.options.showEffect === 'function') {
            this.options.showEffect(this.element);
        }
    },

    hide: function () {
        this.visible = false;
        if (this.modal) {
            Windows.removeModalWindow(this);
            this.modal = false;
        }
        if (typeof this.options.hideEffect === 'function') {
            this.options.hideEffect(this.element);
        } else {
            this.element.style.display = 'none';
        }
    },

    close: function () {
        if (!this.visible) return;
        if (this.options.closeCallback && !this.options.closeCallback(this)) return;

        this.hide();

        if (typeof this.options.onClose === 'function') {
            this.options.onClose(this);
        }

        if (this.options.destroyOnClose) {
            this.destroy();
        }
    },

    destroy: function () {
        Windows.unregister(this);
        if (this.element && this.element.parentNode) {
            this.element.parentNode.removeChild(this.element);
        }
    },

    minimize: function () {
        var row2 = document.getElementById(this.getId() + '_row2');
        if (!row2) return;
        if (!this.minimized) {
            this.minimized     = true;
            this._r2Height     = row2.offsetHeight;
            row2.style.display = 'none';
        } else {
            this.minimized     = false;
            row2.style.display = '';
        }
    },

    maximize: function () {
        if (this.minimized) return;
        if (this._storedLocation) {
            this._restoreLocation();
        } else {
            this._storeLocation();
            var scrollY = window.scrollY || 0;
            var scrollX = window.scrollX || 0;
            var vw = window.innerWidth  || document.documentElement.clientWidth;
            var vh = window.innerHeight || document.documentElement.clientHeight;
            this.element.style.top   = scrollY + 'px';
            this.element.style.left  = scrollX + 'px';
            this.element.style.width = vw + 'px';
            if (this.content) this.content.style.height = vh + 'px';
        }
    },

    isMinimized: function () { return !!this.minimized; },
    isMaximized: function () { return !!this._storedLocation; },

    _storeLocation: function () {
        this._storedLocation = {
            top:    this.element.style.top,
            left:   this.element.style.left,
            width:  this.width,
            height: this.height
        };
    },

    _restoreLocation: function () {
        if (!this._storedLocation) return;
        this.element.style.top  = this._storedLocation.top;
        this.element.style.left = this._storedLocation.left;
        this.setSize(this._storedLocation.width, this._storedLocation.height);
        this._storedLocation = null;
    },

    /* Original 3-table HTML structure — required for magento.css to apply correctly */
    _createWindow: function (id) {
        var cn  = this.options.className;
        var win = document.createElement('div');
        win.id  = id;
        win.className = 'dialog' + (this.options.windowClassName ? ' ' + this.options.windowClassName : '');
        win.style.cssText = 'display:none;position:absolute;z-index:' + this.options.zIndex;

        var closeDiv = this.options.closable
            ? '<div class="' + cn + '_close" id="' + id + '_close" onclick="Windows.close(\'' + id + '\', event)"></div>'
            : '';
        var minDiv = this.options.minimizable
            ? '<div class="' + cn + '_minimize" id="' + id + '_minimize" onclick="Windows.minimize(\'' + id + '\', event)"></div>'
            : '';
        var maxDiv = this.options.maximizable
            ? '<div class="' + cn + '_maximize" id="' + id + '_maximize" onclick="Windows.maximize(\'' + id + '\', event)"></div>'
            : '';
        var seAttr = this.options.resizable
            ? 'class="' + cn + '_sizer" id="' + id + '_sizer"'
            : 'class="' + cn + '_se"';

        win.innerHTML =
            closeDiv + minDiv + maxDiv +
            '<a href="#" id="' + id + '_focus_anchor" style="position:absolute;width:0;height:0;overflow:hidden;outline:0"></a>' +
            '<table id="' + id + '_row1" class="top table_window"><tbody><tr>' +
                '<td class="' + cn + '_nw"></td>' +
                '<td class="' + cn + '_n"><div id="' + id + '_top" class="' + cn + '_title title_window">&nbsp;</div></td>' +
                '<td class="' + cn + '_ne"></td>' +
            '</tr></tbody></table>' +
            '<table id="' + id + '_row2" class="mid table_window"><tbody><tr>' +
                '<td class="' + cn + '_w"></td>' +
                '<td id="' + id + '_table_content" class="' + cn + '_content" valign="top">' +
                    '<div id="' + id + '_content" class="' + cn + '_content"></div>' +
                '</td>' +
                '<td class="' + cn + '_e"></td>' +
            '</tr></tbody></table>' +
            '<table id="' + id + '_row3" class="bot table_window"><tbody><tr>' +
                '<td class="' + cn + '_sw"></td>' +
                '<td class="' + cn + '_s"><div id="' + id + '_bottom" class="status_bar"><span style="float:left;width:1px;height:1px"></span></div></td>' +
                '<td ' + seAttr + '></td>' +
            '</tr></tbody></table>';

        document.body.insertBefore(win, document.body.firstChild);
        return win;
    },

    _initDragging: function () {
        var self   = this;
        var topRow = document.getElementById(this.getId() + '_row1');
        if (!topRow) return;

        topRow.style.cursor = 'move';

        topRow.addEventListener('mousedown', function (e) {
            var closeBtn = document.getElementById(self.getId() + '_close');
            if (closeBtn && closeBtn.contains(e.target)) return;

            var startX   = e.clientX;
            var startY   = e.clientY;
            var origLeft = parseFloat(self.element.style.left) || 0;
            var origTop  = parseFloat(self.element.style.top)  || 0;

            function onMove(e) {
                self.element.style.left = (origLeft + e.clientX - startX) + 'px';
                self.element.style.top  = (origTop  + e.clientY - startY) + 'px';
            }
            function onUp() {
                document.removeEventListener('mousemove', onMove);
                document.removeEventListener('mouseup',  onUp);
            }
            document.addEventListener('mousemove', onMove);
            document.addEventListener('mouseup',   onUp);
            e.preventDefault();
        });
    }
};

/* -----------------------------------------------------------------------
 * Dialog namespace
 * --------------------------------------------------------------------- */
var Dialog = {
    dialogId: null,

    confirm: function (content, parameters) {
        content    = content    || '';
        parameters = parameters || {};

        var cn          = parameters.className || 'magento';
        var okLabel     = parameters.okLabel     || 'Ok';
        var cancelLabel = parameters.cancelLabel || 'Cancel';
        var btnClass    = parameters.buttonClass ? parameters.buttonClass + ' ' : '';

        var html =
            '<div class="' + cn + '_message">' + content + '</div>' +
            '<div class="' + cn + '_buttons" style="padding:10px;display:flex;justify-content:flex-end;gap:6px">' +
                '<button type="button" title="' + cancelLabel + '" onclick="Dialog.cancelCallback()" class="' + btnClass + 'cancel_button">' +
                    '<span><span><span>' + cancelLabel + '</span></span></span>' +
                '</button>' +
                '<button type="button" title="' + okLabel + '" onclick="Dialog.okCallback()" class="' + btnClass + 'ok_button">' +
                    '<span><span><span>' + okLabel + '</span></span></span>' +
                '</button>' +
            '</div>';

        return this._openDialog(html, parameters);
    },

    alert: function (content, parameters) {
        content    = content    || '';
        parameters = parameters || {};

        var cn       = parameters.className || 'magento';
        var okLabel  = parameters.okLabel   || 'Ok';
        var btnClass = parameters.buttonClass ? parameters.buttonClass + ' ' : '';

        var html =
            '<div class="' + cn + '_message">' + content + '</div>' +
            '<div class="' + cn + '_buttons" style="padding:10px;display:flex;justify-content:flex-end;gap:6px">' +
                '<button type="button" title="' + okLabel + '" onclick="Dialog.okCallback()" class="' + btnClass + 'ok_button">' +
                    '<span><span><span>' + okLabel + '</span></span></span>' +
                '</button>' +
            '</div>';

        return this._openDialog(html, parameters);
    },

    info: function (content, parameters) {
        content    = content    || '';
        parameters = parameters || {};

        var cn   = parameters.className || 'magento';
        var html = '<div id="modal_dialog_message" class="' + cn + '_message">' + content + '</div>';

        if (parameters.showProgress) {
            html += '<div id="modal_dialog_progress" class="' + cn + '_progress"></div>';
        }

        parameters.ok     = null;
        parameters.cancel = null;

        return this._openDialog(html, parameters);
    },

    setInfoMessage: function (message) {
        var el = document.getElementById('modal_dialog_message');
        if (el) el.innerHTML = message;
    },

    closeInfo: function () {
        Windows.close(this.dialogId);
    },

    _openDialog: function (content, parameters) {
        parameters = parameters || {};

        parameters.id = parameters.id || ('modal_dialog_' + Date.now());
        this.dialogId = parameters.id;

        parameters.resizable   = parameters.resizable   || false;
        parameters.minimizable = parameters.minimizable || false;
        parameters.maximizable = parameters.maximizable || false;
        parameters.draggable   = parameters.draggable   !== false;
        parameters.closable    = parameters.closable    !== false;
        parameters.destroyOnClose = true;

        var win = new Window(parameters);
        win.getContent().innerHTML = content;

        win.cancelCallback = parameters.cancel || parameters.onCancel || null;
        win.okCallback     = parameters.ok     || parameters.onOk     || null;

        win.showCenter(true, parameters.top, parameters.left);

        return win;
    },

    okCallback: function () {
        var win = Windows.focusedWindow;
        if (!win) return;
        if (!win.okCallback || win.okCallback(win)) {
            win.close();
        }
    },

    cancelCallback: function () {
        var win = Windows.focusedWindow;
        if (!win) return;
        win.close();
        if (win.cancelCallback) {
            win.cancelCallback(win);
        }
    }
};
