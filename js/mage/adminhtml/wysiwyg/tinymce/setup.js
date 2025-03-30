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
 * @copyright   Copyright (c) 2023-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

var tinyMceWysiwygSetup = Class.create();
tinyMceWysiwygSetup.prototype =
{
    mediaBrowserCallback: null,
    mediaBrowserMetal: null,
    mediaBrowserValue: null,

    openmagePluginsOptions: $H({}),

    initialize: function (htmlId, config) {
        this.id = htmlId;
        this.selector = 'textarea#' + htmlId;
        this.config = config;
        varienGlobalEvents.attachEventHandler('tinymceChange', this.onChangeContent.bind(this));

        if (typeof tinyMceEditors === 'undefined') {
            window.tinyMceEditors = $H({});
        }
        tinyMceEditors.set(this.id, this);
    },

    setup: function (mode) {
        var self = this;

        if (this.config.widget_plugin_src) {
            tinymce.PluginManager.load('openmagewidget', this.config.widget_plugin_src);
            this.openmagePluginsOptions.set('openmagewidget', {
                'widget_window_url': this.config.widget_window_url
            });
        }

        if (this.config.plugins) {
            (this.config.plugins).each(function (plugin) {
                tinymce.PluginManager.load(plugin.name, plugin.src);
                self.openmagePluginsOptions.set(plugin.name, plugin.options);
            });
        }

        tinymce.init(this.getSettings(mode));
    },

    getSettings: function (mode) {
        var plugins = 'autoresize accordion searchreplace visualblocks visualchars anchor code lists advlist fullscreen pagebreak table wordcount directionality image charmap link media nonbreaking help';
        var toolbar = 'undo redo | bold italic underline strikethrough | insertfile image media template link anchor codesample | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | fontfamily fontsize blocks | pagebreak | charmap | fullscreen preview save print | ltr rtl'

        // load and add to toolbar openmagePlugins
        if (this.openmagePluginsOptions) {
            var openmageToolbarButtons = '';
            this.openmagePluginsOptions.each(function (plugin, key) {
                plugins = plugin.key + ' ' + plugins;
                openmageToolbarButtons = plugin.key + ' ' + openmageToolbarButtons;
            });
            toolbar = openmageToolbarButtons + ' | ' + toolbar;
        }

        var settings = {
            license_key: "gpl",
            selector: this.selector,
            config: this.config,
            valid_children: '+body[style]',
            custom_elements:"style,~style",
            protect: [
                /[\S]?<script[\s\S]*?>[\s\S]*?<\/script[\s\S]*?>[\S]?/ig
            ],
            menu: {
                insert: {
                    title: 'Insert',
                    items: 'image link media addcomment pageembed template codesample inserttable | openmagevariable openmagewidget | charmap emoticons hr | pagebreak nonbreaking anchor tableofcontents | insertdatetime'
                }
            },
            menubar: 'file edit view insert format tools table help',
            plugins: plugins,
            toolbar: toolbar,
            language: this.config.lang,
            paste_as_text: true,
            file_picker_types: 'file image media',
            automatic_uploads: false,
            branding: false,
            promotion: false,
            convert_unsafe_embeds: true, // default in TinyMCE v7.0
            convert_urls: false,
            relative_urls: true,
            skin: this.config.skin,
            min_height: 460,
            urlconverter_callback: (url, node, on_save, name) => {
                // some callback here to convert urls
                //url = this.decodeContent(url);
                return url;
            },
            setup: (editor) => {
                var onChange;

                editor.on('BeforeSetContent', function (evt) {
                    varienGlobalEvents.fireEvent('tinymceBeforeSetContent', evt);
                });

                editor.on('SaveContent', function (evt) {
                    varienGlobalEvents.fireEvent('tinymceSaveContent', evt);
                });

                editor.on('Paste', function (ed, e, o) {
                    varienGlobalEvents.fireEvent('tinymcePaste', o);
                });

                editor.on('PostProcess', function (evt) {
                    varienGlobalEvents.fireEvent('tinymceSaveContent', evt);
                });

                editor.on('setContent', function (evt) {
                    varienGlobalEvents.fireEvent('tinymceSetContent', evt);
                });

                onChange = function (evt) {
                    varienGlobalEvents.fireEvent('tinymceChange', evt);
                };

                editor.on('Change', onChange);
                editor.on('keyup', onChange);

                editor.on('ExecCommand', function (cmd, ui, val) {
                    varienGlobalEvents.fireEvent('tinymceExecCommand', cmd);
                });

                editor.on('init', function (args) {
                    varienGlobalEvents.fireEvent('wysiwygEditorInitialized', args.target);
                });
            }
        }

        // Set the document base URL
        if (this.config.document_base_url) {
            settings.document_base_url = this.config.document_base_url;
        }

        if (this.config.files_browser_window_url) {
            settings.file_picker_callback = (callback, value, meta) => {
                varienGlobalEvents.fireEvent("open_browser_callback", { callback: callback, value: value, meta: meta });
            };
        }
        return settings;
    },

    openFileBrowser: function (o) {
        var typeTitle;
        var storeId = this.config.store_id !== null ? this.config.store_id : 0;
        var wUrl = this.config.files_browser_window_url +
            'target_element_id/' + this.id + '/' +
            'store/' + storeId + '/';

        this.mediaBrowserCallback = o.callback;
        this.mediaBrowserMeta = o.meta;
        this.mediaBrowserValue = o.value;

        if (typeof (o.meta.filetype) != 'undefined' && o.meta.filetype == "image") {
            typeTitle = 'image' == o.meta.filetype ? this.translate('Insert Image...') : this.translate('Insert Media...');
            wUrl = wUrl + "type/" + o.meta.filetype + "/";
        } else {
            typeTitle = this.translate('Insert File...');
        }

        MediabrowserUtility.openDialog(wUrl, false, false, typeTitle, {
            onBeforeShow: function (win) {
                win.element.setStyle({ zIndex: 300200 });
            }
        });
    },

    translate: function (string) {
        return 'undefined' != typeof (Translator) ? Translator.translate(string) : string;
    },

    getToggleButton: function () {
        return document.getElementById('toggle' + this.id);
    },

    getPluginButtons: function () {
        return document.querySelectorAll('#buttons' + this.id + ' > button.plugin');
    },

    turnOn: function () {
        this.closePopups();
        this.setup();
        this.getPluginButtons().forEach(function (e) {
            e.hide();
        });
    },

    turnOff: function () {
        this.closePopups();
        if (tinymce.get(this.id)) {
            tinymce.get(this.id).destroy();
        }
        this.getPluginButtons().forEach(function (e) {
            e.show();
        });
    },

    closePopups: function () {
        closeEditorPopup('widget_window' + this.id);
        closeEditorPopup('browser_window' + this.id);
    },

    toggle: function () {
        if (tinymce.get(this.id) === null) {
            this.turnOn();
            return true;
        } else {
            this.turnOff();
            return false;
        }
    },

    onFormValidation: function () {
        if (tinymce.get(this.id)) {
            document.getElementById(this.id).value = tinymce.get(this.id).getContent();
        }
    },

    onChangeContent: function () {
        if (this.config.tab_id) {
            var tab = document.querySelector('a[id$=' + this.config.tab_id + ']');
            if ($(tab) != undefined && $(tab).hasClassName('tab-item-link')) {
                $(tab).addClassName('changed');
            }
        }
    },

    beforeSetContent: function (o) {
        o.content = this.encodeContent(o.content);
    },

    saveContent: function (o) {
        o.content = this.decodeContent(o.content);
    },

    updateTextArea: function () {
        content = tinymce.get(this.id).getContent();
        content = this.decodeContent(content);
        this.getTextArea().value = content;
        this.triggerChange(this.getTextArea());
    },

    getTextArea: function () {
        return document.getElementById(this.id);
    },

    triggerChange: function (element) {
        if ("createEvent" in document) {
            var evt = document.createEvent("HTMLEvents");
            evt.initEvent("change", false, true);
            element.dispatchEvent(evt);
        } else {
            element.fireEvent("onchange");
        }
        return element;
    },

    encodeContent: function (content) {
        if (this.config.add_widgets) {
            content = this.encodeWidgets(content);
            content = this.encodeDirectives(content);
        } else if (this.config.add_directives) {
            content = this.encodeDirectives(content);
        }
        return content;
    },

    decodeContent: function (content) {
        if (this.config.add_widgets) {
            content = this.decodeWidgets(content);
            content = this.decodeDirectives(content);
        } else if (this.config.add_directives) {
            content = this.decodeDirectives(content);
        }
        return content;
    },

    // retrieve directives URL with substituted directive value
    makeDirectiveUrl: function (directive) {
        return this.config.directives_url.replace('directive', 'directive/___directive/' + directive);
    },

    encodeDirectives: function (content) {
        // collect all HTML tags with attributes that contain directives
        return content.gsub(/<([a-z0-9\-\_]+.+?)([a-z0-9\-\_]+=".*?\{\{.+?\}\}.*?".+?)>/i, function (match) {
            var attributesString = match[2];
            // process tag attributes string
            attributesString = attributesString.gsub(/([a-z0-9\-\_]+)="(.*?)(\{\{.+?\}\})(.*?)"/i, function (m) {
                return m[1] + '="' + m[2] + this.makeDirectiveUrl(Base64.mageEncode(m[3])) + m[4] + '"';
            }.bind(this));

            return '<' + match[1] + attributesString + '>';

        }.bind(this));
    },

    encodeWidgets: function (content) {
        return content.gsub(/\{\{widget(.*?)\}\}/i, function (match) {
            var attributes = this.parseAttributesString(match[1]);
            if (attributes.type) {
                var placeholderFilename = attributes.type.replace(/\//g, "__") + ".gif";
                if (!this.widgetPlaceholderExist(placeholderFilename)) {
                    placeholderFilename = 'default.gif';
                }
                var imageSrc = this.config.widget_images_url + placeholderFilename;
                var imageHtml = '<img';
                imageHtml += ' id="' + Base64.idEncode(match[0]) + '"';
                imageHtml += ' src="' + imageSrc + '"';
                imageHtml += ' title="' + match[0].replace(/\{\{/g, '{').replace(/\}\}/g, '}').replace(/\"/g, '&quot;') + '"';
                imageHtml += '>';

                return imageHtml;
            }
        }.bind(this));
    },

    decodeDirectives: function (content) {
        // escape special chars in directives url to use it in regular expression
        var url = this.makeDirectiveUrl('%directive%').replace(/([$^.?*!+:=()\[\]{}|\\])/g, '\\$1');
        var reg = new RegExp(url.replace('%directive%', '([a-zA-Z0-9,_-]+)'));
        return content.gsub(reg, function (match) {
            return Base64.mageDecode(match[1]);
        }.bind(this));
    },

    decodeWidgets: function (content) {
        return content.gsub(/<img([^>]+id=\"[^>]+)>/i, function (match) {
            var attributes = this.parseAttributesString(match[1]);
            if (attributes.id) {
                var widgetCode = Base64.idDecode(attributes.id);
                if (widgetCode.indexOf('{{widget') != -1) {
                    return widgetCode;
                }
                return match[0];
            }
            return match[0];
        }.bind(this));
    },

    parseAttributesString: function (attributes) {
        var result = {};
        attributes.gsub(/(\w+)(?:\s*=\s*(?:(?:"((?:\\.|[^"\\])*)")|(?:'((?:\\.|[^'\\])*)')|([^>\s]+)))?/, function (match) {
            result[match[1]] = match[2];
        });
        return result;
    },

    widgetPlaceholderExist: function (filename) {
        return this.config.widget_placeholders.indexOf(filename) != -1;
    },

    getMediaBrowserCallback: function () {
        return this.mediaBrowserCallback;
    }
};
