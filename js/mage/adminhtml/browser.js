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
MediabrowserUtility = {
    dialogWindow: null,
    overlayEl: null,
    dialogEl: null,

    openDialog: function(url, width, height, title, options) {
        var browserWin = document.getElementById('browser_window');
        if (browserWin && typeof Windows !== 'undefined') {
            Windows.focus('browser_window');
            return;
        }

        var opts = Object.assign({
            closable:          true,
            resizable:         false,
            draggable:         true,
            className:         'magento',
            windowClassName:   'popup-window',
            title:             title || 'Insert File...',
            top:               50,
            width:             width || 950,
            height:            height || 600,
            zIndex:            (options && options.zIndex) || 1000,
            recenterAuto:      false,
            id:                'browser_window',
            onClose:           this.closeDialog.bind(this)
        }, options || {});

        if (typeof Dialog !== 'undefined') {
            this.dialogWindow = Dialog.info(null, Object.assign(opts, {
                hideEffect: function(el) { el.style.display = 'none'; },
                showEffect: function(el) { el.style.display = ''; }
            }));

            fetch(url)
                .then(function(response) { return response.text(); })
                .then(function(html) {
                    var msgEl = document.getElementById('modal_dialog_message');
                    if (msgEl) {
                        msgEl.innerHTML = html;
                        var scripts = msgEl.querySelectorAll('script');
                        scripts.forEach(function(script) {
                            var newScript = document.createElement('script');
                            if (script.src) {
                                newScript.src = script.src;
                            } else {
                                newScript.textContent = script.textContent;
                            }
                            document.head.appendChild(newScript);
                        });
                    }
                });
        } else {
            // Fallback: simple overlay modal
            this._createFallbackDialog(url, opts);
        }
    },

    _createFallbackDialog: function(url, opts) {
        var self = this;

        // Overlay
        var overlay = document.createElement('div');
        overlay.id = 'browser_overlay';
        overlay.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:' + (opts.zIndex - 1) + ';';
        document.body.appendChild(overlay);
        this.overlayEl = overlay;

        // Dialog container
        var dialog = document.createElement('div');
        dialog.id = opts.id || 'browser_window';
        dialog.className = (opts.windowClassName || 'popup-window') + ' ' + (opts.className || '');
        dialog.style.cssText = 'position:fixed;top:' + opts.top + 'px;left:50%;transform:translateX(-50%);'
            + 'width:' + opts.width + 'px;height:' + opts.height + 'px;'
            + 'z-index:' + opts.zIndex + ';background:#fff;border:1px solid #999;'
            + 'box-shadow:0 4px 20px rgba(0,0,0,0.3);overflow:hidden;';

        // Title bar
        var titleBar = document.createElement('div');
        titleBar.style.cssText = 'padding:8px 12px;background:#f0f0f0;border-bottom:1px solid #ccc;display:flex;justify-content:space-between;align-items:center;';
        titleBar.innerHTML = '<span style="font-weight:bold;">' + (opts.title || '') + '</span>';

        if (opts.closable !== false) {
            var closeBtn = document.createElement('button');
            closeBtn.type = 'button';
            closeBtn.textContent = '\u00D7';
            closeBtn.style.cssText = 'border:none;background:none;font-size:20px;cursor:pointer;padding:0 4px;';
            closeBtn.addEventListener('click', function() {
                self.closeDialog();
            });
            titleBar.appendChild(closeBtn);
        }
        dialog.appendChild(titleBar);

        // Content area
        var content = document.createElement('div');
        content.id = 'modal_dialog_message';
        content.style.cssText = 'overflow:auto;height:calc(100% - 40px);';
        dialog.appendChild(content);

        document.body.appendChild(dialog);
        this.dialogEl = dialog;

        // Clicking overlay closes dialog
        overlay.addEventListener('click', function() {
            self.closeDialog();
        });

        // Set dialogWindow to a closeable object
        this.dialogWindow = {
            close: function() {
                self._removeFallbackDialog();
            }
        };

        // Fetch content
        fetch(url)
            .then(function(response) { return response.text(); })
            .then(function(html) {
                content.innerHTML = html;
                var scripts = content.querySelectorAll('script');
                scripts.forEach(function(script) {
                    var newScript = document.createElement('script');
                    if (script.src) {
                        newScript.src = script.src;
                    } else {
                        newScript.textContent = script.textContent;
                    }
                    document.head.appendChild(newScript);
                });
            });
    },

    _removeFallbackDialog: function() {
        if (this.overlayEl && this.overlayEl.parentNode) {
            this.overlayEl.parentNode.removeChild(this.overlayEl);
            this.overlayEl = null;
        }
        if (this.dialogEl && this.dialogEl.parentNode) {
            this.dialogEl.parentNode.removeChild(this.dialogEl);
            this.dialogEl = null;
        }
        this.dialogWindow = null;
    },

    closeDialog: function(win) {
        if (!win) {
            win = this.dialogWindow;
        }
        if (win) {
            win.close();
        }
        // Clean up fallback elements if they exist
        this._removeFallbackDialog();
    }
};

function Mediabrowser(setup) {
    this.targetElementId = null;
    this.contentsUrl = null;
    this.onInsertUrl = null;
    this.newFolderUrl = null;
    this.deleteFolderUrl = null;
    this.deleteFilesUrl = null;
    this.headerText = null;
    this.tree = null;
    this.currentNode = null;
    this.storeId = null;

    this.newFolderPrompt = setup.newFolderPrompt;
    this.deleteFolderConfirmationMessage = setup.deleteFolderConfirmationMessage;
    this.deleteFileConfirmationMessage = setup.deleteFileConfirmationMessage;
    this.targetElementId = setup.targetElementId;
    this.contentsUrl = setup.contentsUrl;
    this.onInsertUrl = setup.onInsertUrl;
    this.newFolderUrl = setup.newFolderUrl;
    this.deleteFolderUrl = setup.deleteFolderUrl;
    this.deleteFilesUrl = setup.deleteFilesUrl;
    this.headerText = setup.headerText;
}

Mediabrowser.prototype = {
    setTree: function (tree) {
        this.tree = tree;
        this.currentNode = tree.getRootNode();
    },

    getTree: function (tree) {
        return this.tree;
    },

    selectFolder: function (node, event) {
        var self = this;
        this.currentNode = node;
        this.hideFileButtons();
        this.activateBlock('contents');

        if (node.id == 'root') {
            this.hideElement('button_delete_folder');
        } else {
            this.showElement('button_delete_folder');
        }

        this.updateHeader(this.currentNode);
        this.drawBreadcrumbs(this.currentNode);

        this.showElement('loading-mask');

        var params = new URLSearchParams();
        params.append('node', this.currentNode.id);
        params.append('form_key', window.FORM_KEY);

        fetch(this.contentsUrl, {
            method: 'POST',
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            body: params
        })
        .then(function(response) { return response.text(); })
        .then(function(responseText) {
            try {
                self.currentNode.select();
                self._handleAjaxSuccess(responseText);
                self.hideElement('loading-mask');
                var contentsEl = document.getElementById('contents');
                if (contentsEl != undefined) {
                    contentsEl.innerHTML = responseText;
                    // innerHTML does not execute <script> tags; re-create them
                    // so any inline scripts in the AJAX response still run
                    contentsEl.querySelectorAll('script').forEach(function(script) {
                        var newScript = document.createElement('script');
                        if (script.src) {
                            newScript.src = script.src;
                        } else {
                            newScript.textContent = script.textContent;
                        }
                        document.head.appendChild(newScript);
                    });
                    var fileDivs = document.querySelectorAll('div.filecnt');
                    fileDivs.forEach(function(s) {
                        s.addEventListener('click', self.selectFile.bind(self));
                        s.addEventListener('dblclick', self.insert.bind(self));
                    });
                }
            } catch(e) {
                alert(e.message);
            }
        });
    },

    selectFolderById: function (nodeId) {
        var node = this.tree.getNodeById(nodeId);
        if (node.id) {
            this.selectFolder(node);
        }
    },

    selectFile: function (event) {
        var div = event.target.closest('DIV');
        var others = document.querySelectorAll('div.filecnt.selected');
        others.forEach(function(e) {
            if (e.id !== div.id) {
                e.classList.remove('selected');
            }
        });
        div.classList.toggle('selected');
        if (div.classList.contains('selected')) {
            this.showFileButtons();
        } else {
            this.hideFileButtons();
        }
    },

    showFileButtons: function () {
        this.showElement('button_delete_files');
        this.showElement('button_insert_files');
    },

    hideFileButtons: function () {
        this.hideElement('button_delete_files');
        this.hideElement('button_insert_files');
    },

    handleUploadComplete: function(files) {
        var completedRows = document.querySelectorAll('div[class*="file-row complete"]');
        completedRows.forEach(function(e) {
            e.remove();
        });
        this.selectFolder(this.currentNode);
    },

    insert: function(event) {
        var self = this;
        var div;
        if (event != undefined) {
            div = event.target.closest('DIV');
        } else {
            var selected = document.querySelectorAll('div.selected');
            selected.forEach(function (e) {
                div = document.getElementById(e.id);
            });
        }
        if (document.getElementById(div.id) == undefined) {
            return false;
        }
        var targetEl = this.getTargetElement();
        if (!targetEl) {
            alert("Target element not found for content update");
            if (typeof Windows !== 'undefined') {
                Windows.close('browser_window');
            } else {
                MediabrowserUtility.closeDialog();
            }
            return;
        }

        var params = new URLSearchParams();
        params.append('filename', div.id);
        params.append('node', this.currentNode.id);
        params.append('store', this.storeId);
        params.append('form_key', window.FORM_KEY);

        if (targetEl.tagName && targetEl.tagName.toLowerCase() == 'textarea') {
            params.append('as_is', '1');
        }

        fetch(this.onInsertUrl, {
            method: 'POST',
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            body: params
        })
        .then(function(response) { return response.text(); })
        .then(function(responseText) {
            try {
                self._handleAjaxSuccess(responseText);
                if (self.getMediaBrowserCallback()) {
                    self.blur();
                }
                if (typeof Windows !== 'undefined') {
                    Windows.close('browser_window');
                } else {
                    MediabrowserUtility.closeDialog();
                }
                if (targetEl.tagName && targetEl.tagName.toLowerCase() == 'input') {
                    targetEl.value = responseText;
                } else if (targetEl.tagName && targetEl.tagName.toLowerCase() == 'textarea') {
                    updateElementAtCursor(targetEl, responseText);
                } else {
                    targetEl(responseText);
                }
            } catch (e) {
                alert(e.message);
            }
        });
    },

    /**
     * Find document target element in next order:
     *  in acive file browser opener:
     *  - input field with ID: "src" in opener window
     *  - input field with ID: "href" in opener window
     *  in document:
     *  - element with target ID
     *
     * return HTMLelement | null
     */
    getTargetElement: function() {
        if (typeof tinyMCE !== 'undefined' && tinyMCE.get(this.targetElementId)) {
            var callbak = this.getMediaBrowserCallback();
            if (callbak) {
                return callbak;
            } else {
                return null;
            }
        } else {
            return document.getElementById(this.targetElementId);
        }
    },

    /**
     * return object|null
     */
    getMediaBrowserCallback: function() {
        if (typeof tinyMCE !== 'undefined' && tinyMCE.get(this.targetElementId) && typeof tinyMceEditors !== 'undefined') {
            return tinyMceEditors.get(this.targetElementId).getMediaBrowserCallback();
        }
        return null;
    },

    newFolder: function() {
        var self = this;
        var folderName = prompt(this.newFolderPrompt);
        if (!folderName) {
            return false;
        }

        var params = new URLSearchParams();
        params.append('name', folderName);
        params.append('form_key', window.FORM_KEY);

        fetch(this.newFolderUrl, {
            method: 'POST',
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            body: params
        })
        .then(function(response) { return response.text(); })
        .then(function(responseText) {
            try {
                self._handleAjaxSuccess(responseText);
                var response;
                try {
                    response = JSON.parse(responseText);
                } catch(e) {
                    return;
                }
                if (response && typeof Ext !== 'undefined' && typeof Ext.tree !== 'undefined' && typeof Ext.tree.AsyncTreeNode !== 'undefined') {
                    var newNode = new Ext.tree.AsyncTreeNode({
                        text: response.short_name,
                        draggable: false,
                        id: response.id,
                        expanded: true
                    });
                    var child = self.currentNode.appendChild(newNode);
                    self.tree.expandPath(child.getPath(), '', function(success, node) {
                        self.selectFolder(node);
                    });
                }
            } catch (e) {
                alert(e.message);
            }
        });
    },

    deleteFolder: function() {
        var self = this;
        if (!confirm(this.deleteFolderConfirmationMessage)) {
            return false;
        }

        fetch(this.deleteFolderUrl, {
            method: 'POST',
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            body: new URLSearchParams({form_key: window.FORM_KEY})
        })
        .then(function(response) { return response.text(); })
        .then(function(responseText) {
            try {
                self._handleAjaxSuccess(responseText);
                var parent = self.currentNode.parentNode;
                parent.removeChild(self.currentNode);
                self.selectFolder(parent);
            }
            catch (e) {
                alert(e.message);
            }
        });
    },

    deleteFiles: function() {
        var self = this;
        if (!confirm(this.deleteFileConfirmationMessage)) {
            return false;
        }
        var ids = [];
        var selected = document.querySelectorAll('div.selected');
        selected.forEach(function (e) {
            ids.push(e.id);
        });

        var params = new URLSearchParams();
        params.append('files', JSON.stringify(ids));
        params.append('form_key', window.FORM_KEY);

        fetch(this.deleteFilesUrl, {
            method: 'POST',
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            body: params
        })
        .then(function(response) { return response.text(); })
        .then(function(responseText) {
            try {
                self._handleAjaxSuccess(responseText);
                self.selectFolder(self.currentNode);
            } catch(e) {
                alert(e.message);
            }
        });
    },

    drawBreadcrumbs: function(node) {
        var existing = document.getElementById('breadcrumbs');
        if (existing != undefined) {
            existing.remove();
        }
        if (node.id == 'root') {
            return;
        }
        var path = node.getPath().split('/');
        var breadcrumbs = '';
        for (var i = 0, length = path.length; i < length; i++) {
            if (path[i] == '') {
                continue;
            }
            var currNode = this.tree.getNodeById(path[i]);
            if (currNode.id) {
                breadcrumbs += '<li>';
                breadcrumbs += '<a href="#" onclick="MediabrowserInstance.selectFolderById(\'' + currNode.id + '\');">' + currNode.text + '</a>';
                if (i < (length - 1)) {
                    breadcrumbs += ' <span>/</span>';
                }
                breadcrumbs += '</li>';
            }
        }

        if (breadcrumbs != '') {
            breadcrumbs = '<ul class="breadcrumbs" id="breadcrumbs">' + breadcrumbs + '</ul>';
            var contentHeader = document.getElementById('content_header');
            if (contentHeader) {
                contentHeader.insertAdjacentHTML('afterend', breadcrumbs);
            }
        }
    },

    updateHeader: function(node) {
        var header = (node.id == 'root' ? this.headerText : node.text);
        var headerEl = document.getElementById('content_header_text');
        if (headerEl != undefined) {
            headerEl.innerHTML = header;
        }
    },

    activateBlock: function(id) {
        this.showElement(id);
    },

    hideElement: function(id) {
        var el = document.getElementById(id);
        if (el != undefined) {
            el.classList.add('no-display');
            el.style.display = 'none';
        }
    },

    showElement: function(id) {
        var el = document.getElementById(id);
        if (el != undefined) {
            el.classList.remove('no-display');
            el.style.display = '';
        }
    },

    onAjaxSuccess: function(transport) {
        this._handleAjaxSuccess(transport.responseText || transport);
    },

    _handleAjaxSuccess: function(responseText) {
        var response;
        try {
            response = JSON.parse(responseText);
        } catch(e) {
            return;
        }
        if (response.error) {
            throw response;
        } else if (response.ajaxExpired && response.ajaxRedirect) {
            setLocation(response.ajaxRedirect);
        }
    }
};
