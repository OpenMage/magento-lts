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

if(!window.Flex) {
    alert('Flex library not loaded');
} else {
    Flex.Uploader = Class.create();
    Flex.Uploader.prototype = {
        flex: null,
        uploader:null,
        filters:null,
        containerId:null,
        flexContainerId:null,
        container:null,
        files:null,
        fileRowTemplate:null,
        fileProgressTemplate:null,
        templatesPattern: /(^|.|\r|\n)(\{\{(.*?)\}\})/,
        onFilesComplete: false,
        onFileProgress: false,
        onFileRemove: false,
        initialize: function(containerId, uploaderSrc, config) {
            this.containerId = containerId;
            this.container   = $(containerId);

            this.container.controller = this;

            this.config = config;

            this.flexContainerId = this.containerId + '-flash';
            new Insertion.Top(
                window.document.body,
                '<div id="'+this.flexContainerId+'" class="flex" style="position:absolute;"></div>'
            );

            this.flex = new Flex.Object({
                width:  1,
                height: 1,
                src:    uploaderSrc,
                wmode: 'transparent'
            });
            this.getInnerElement('browse').disabled = true;
            this.getInnerElement('upload').disabled = true;
            this.fileRowTemplate = new Template(
                this.getInnerElement('template').innerHTML,
                this.templatesPattern
            );

            this.fileProgressTemplate = new Template(
                this.getInnerElement('template-progress').innerHTML,
                this.templatesPattern
            );

            this.flex.onBridgeInit = this.handleBridgeInit.bind(this);
            if (this.flex.detectFlashVersion(9, 0, 28)) {
                this.flex.apply(this.flexContainerId);
            } else {
                this.getInnerElement('browse').hide();
                this.getInnerElement('upload').hide();
                this.getInnerElement('install-flash').show();
            }
        },
        getInnerElement: function(elementName) {
            return $(this.containerId + '-' + elementName);
        },
        getFileId: function(file) {
            var id;
            if(typeof file == 'object') {
                id = file.id;
            } else {
                id = file;
            }
            return this.containerId + '-file-' + id;
        },
        getDeleteButton: function(file) {
            return $(this.getFileId(file) + '-delete');
        },
        handleBridgeInit: function() {
            this.uploader = this.flex.getBridge().getUpload();
            if (this.config.filters) {
                $H(this.config.filters).each(function(pair) {
                    this.uploader.addTypeFilter(pair.key, pair.value.label, pair.value.files);
                }.bind(this));
                delete(this.config.filters);
                this.uploader.setUseTypeFilter(true);
            }

            this.uploader.setConfig(this.config);
            this.uploader.addEventListener('select',    this.handleSelect.bind(this));
            this.uploader.addEventListener('complete',  this.handleComplete.bind(this));
            this.uploader.addEventListener('progress',  this.handleProgress.bind(this));
            this.uploader.addEventListener('error',     this.handleError.bind(this));
            this.getInnerElement('browse').disabled = false;
            this.getInnerElement('upload').disabled = false;
        },
        browse: function() {
            this.uploader.browse();
        },
        upload: function() {
            this.uploader.upload();
            this.files = this.uploader.getFilesInfo();
            this.updateFiles();
        },
        removeFile: function(id) {
            this.uploader.removeFile(id);
            $(this.getFileId(id)).remove();
            if (this.onFileRemove) {
                this.onFileRemove(id);
            }
        },
        handleSelect: function (event) {
            this.files = event.getData().files;
            this.updateFiles();
            this.getInnerElement('upload').show();
        },
        handleProgress: function (event) {
            var file = event.getData().file;
            this.updateFile();
            if (this.onFileProgress) {
                this.onFileProgress(file);
            }
        },
        handleError: function (event) {
            this.updateFile(event.getData().file);
        },
        handleComplete: function (event) {
            this.files = event.getData().files;
            this.updateFiles();
            if (this.onFilesComplete) {
                this.onFilesComplete(this.files);
            }
        },
        handleRemove: function (event) {
            this.files = this.uploader.getFilesInfo();
            this.updateFiles();
        },
        updateFiles: function () {
            this.files.each(function(file) {
                this.updateFile(file);
            }.bind(this));
        },
        updateFile:  function (file) {
            if (!$(this.getFileId(file))) {
                new Insertion.Bottom(
                    this.container,
                    this.fileRowTemplate.evaluate(this.getFileVars(file))
                );
            }

            if (file.status == 'full_complete' && file.response.isJSON()) {
                var response = file.response.evalJSON();
                if (typeof response == 'object') {
                    if (typeof response.cookie == 'object') {
                        var date = new Date();
                        date.setTime(date.getTime()+(parseInt(response.cookie.lifetime)*1000));

                        document.cookie = escape(response.cookie.name) + "="
                            + escape(response.cookie.value)
                            + "; expires=" + date.toGMTString()
                            + (response.cookie.path.blank() ? "" : "; path=" + response.cookie.path)
                            + (response.cookie.domain.blank() ? "" : "; domain=" + response.cookie.domain);
                    }
                }
            }

            var progress = $(this.getFileId(file)).getElementsByClassName('progress-text')[0];
            if ((file.status=='progress') || (file.status=='complete')) {
                $(this.getFileId(file)).addClassName('progress');
                $(this.getFileId(file)).removeClassName('new');
                $(this.getFileId(file)).removeClassName('error');
                if (file.progress && file.progress.total) {
                    progress.update(this.fileProgressTemplate.evaluate(this.getFileProgressVars(file)));
                } else {
                    progress.update('');
                }
                this.getDeleteButton(file).hide();
            } else if (file.status=='error') {
                $(this.getFileId(file)).addClassName('error');
                $(this.getFileId(file)).removeClassName('progress');
                $(this.getFileId(file)).removeClassName('new');
                progress.update(this.errorText(file));
                this.getDeleteButton(file).show();
            } else if (file.status=='full_complete') {
                $(this.getFileId(file)).addClassName('complete');
                $(this.getFileId(file)).removeClassName('progress');
                $(this.getFileId(file)).removeClassName('error');
                progress.update(this.translate('Complete'));
            }
        },
        getDebugStr: function(obj) {
             return Object.toJSON(obj).replace('&', '&amp;').replace('>', '&gt;').replace('<', '&lt;');
        },
        getFileVars: function(file) {
            return {
                id      : this.getFileId(file),
                fileId  : file.id,
                name    : file.name,
                size    : this.formatSize(file.size)
            };
        },
        getFileProgressVars: function(file) {
            return {
                total    : this.formatSize(file.progress.total),
                uploaded : this.formatSize(file.progress.loaded),
                percent  : this.round((file.progress.loaded/file.progress.total)*100)
            };
        },
        formatSize: function(size) {
            if (size > 1024*1024*1024*1024) {
                return this.round(size/(1024*1024*1024*1024)) + ' ' + this.translate('Tb');
            } else if (size > 1024*1024*1024) {
                return this.round(size/(1024*1024*1024))  + ' ' + this.translate('Gb');
            } else if (size > 1024*1024) {
                return this.round(size/(1024*1024))  + ' ' + this.translate('Mb');
            } else if (size > 1024) {
                return this.round(size/(1024))  + ' ' + this.translate('Kb');
            }
            return size  + ' ' + this.translate('b');
        },
        round: function(number) {
            return Math.round(number*100)/100;
        },
        translate: function(text) {
            try {
                if(Translator){
                   return Translator.translate(text);
                }
            }
            catch(e){}
            return text;
        },
        errorText: function(file) {
            var error = '';

            switch(file.errorCode) {
                case 1: // Size 0
                    error = 'File size should be more than 0 bytes';
                    break;
                case 2: // Http error
                    error = 'Upload HTTP Error';
                    break;
                case 3: // I/O error
                    error = 'Upload I/O Error';
                    break;
                case 4: // Security error
                    error = 'Upload Security Error';
                    break;
                case 5: // SSL self-signed certificate
                    error = 'SSL Error: Invalid or self-signed certificate';
                    break;
            }

            if(error) {
                return this.translate(error);
            }

            return error;
        }
    }
}