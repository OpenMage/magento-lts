/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
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
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

var tinyMceWysiwygSetup = Class.create();
tinyMceWysiwygSetup.prototype =
{
    mediaBrowserOpener: null,
    initialize: function(htmlId, config)
    {
        this.id = htmlId;
        this.config = config;
        varienGlobalEvents.attachEventHandler('tinymceChange', this.onChangeContent.bind(this));
        this.notifyFirebug();
        if(typeof tinyMceEditors == 'undefined') {
            tinyMceEditors = $H({});
        }
        tinyMceEditors.set(this.id, this);
    },

    notifyFirebug: function() {
        if (firebugEnabled() && $('fb' + this.id) == undefined) {
            var noticeHtml = '<ul class="messages message-firebug" id="fb' + this.id + '"><li class="notice-msg">';
                noticeHtml+= '<ul><li>';
                noticeHtml+= '<b>' + this.config.firebug_warning_title + ':</b> ';
                noticeHtml+= this.config.firebug_warning_text;
                noticeHtml+= ' <a id="hidefb' + this.id + '" href="">' + this.config.firebug_warning_anchor + '</a>';
                noticeHtml+= '</li></ul>';
                noticeHtml+= '</li></ul>';
            $('buttons' + this.id).insert({before: noticeHtml});
            Event.observe($('hidefb' + this.id), "click", function(e) {
                $('fb' + this.id).remove();
                Event.stop(e);
            }.bind(this));
        }
    },

    setup: function(mode)
    {
        if (this.config.widget_plugin_src) {
            tinymce.PluginManager.load('magentowidget', this.config.widget_plugin_src);
        }

        if (this.config.plugins) {
            (this.config.plugins).each(function(plugin){
                tinymce.PluginManager.load(plugin.name, plugin.src);
            });
        }

        tinyMCE.init(this.getSettings(mode));
    },

    getSettings: function(mode)
    {
        var plugins = 'safari,pagebreak,style,layer,table,advhr,advimage,emotions,iespell,media,searchreplace,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras';

        if (this.config.widget_plugin_src) {
            plugins = 'magentowidget,' + plugins;
        }

        if (this.config.plugins) {
            var magentoPluginsOptions = $H({});
            var magentoPlugins = '';
            (this.config.plugins).each(function(plugin){
                magentoPlugins = plugin.name + ',' + magentoPlugins;
                magentoPluginsOptions.set(plugin.name, plugin.options);
            });
            if (magentoPlugins) {
                plugins = '-' + magentoPlugins + plugins;
            }
        }

        var settings = {
            mode : (mode != undefined ? mode : 'none'),
            elements : this.id,
            theme : 'advanced',
            plugins : plugins,
            theme_advanced_buttons1 : magentoPlugins + 'magentowidget,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect',
            theme_advanced_buttons2 : 'cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,forecolor,backcolor',
            theme_advanced_buttons3 : 'tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,iespell,media,advhr,|,ltr,rtl,|,fullscreen',
            theme_advanced_buttons4 : 'insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,pagebreak',
            theme_advanced_toolbar_location : 'top',
            theme_advanced_toolbar_align : 'left',
            theme_advanced_statusbar_location : 'bottom',
            theme_advanced_resizing : true,
            convert_urls : false,
            relative_urls : false,
            content_css: this.config.content_css,
            custom_popup_css: this.config.popup_css,
            magentowidget_url: this.config.widget_window_url,
            magentoPluginsOptions: magentoPluginsOptions,
            doctype : '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',

            setup : function(ed) {
                ed.onSubmit.add(function(ed, e) {
                    varienGlobalEvents.fireEvent('tinymceSubmit', e);
                });

                ed.onPaste.add(function(ed, e, o) {
                    varienGlobalEvents.fireEvent('tinymcePaste', o);
                });

                ed.onBeforeSetContent.add(function(ed, o) {
                    varienGlobalEvents.fireEvent('tinymceBeforeSetContent', o);
                });

                ed.onSetContent.add(function(ed, o) {
                    varienGlobalEvents.fireEvent('tinymceSetContent', o);
                });

                ed.onSaveContent.add(function(ed, o) {
                    varienGlobalEvents.fireEvent('tinymceSaveContent', o);
                });

                ed.onChange.add(function(ed, l) {
                    varienGlobalEvents.fireEvent('tinymceChange', l);
                });

                ed.onExecCommand.add(function(ed, cmd, ui, val) {
                    varienGlobalEvents.fireEvent('tinymceExecCommand', cmd);
                });
            }
        };

        if (this.config.files_browser_window_url) {
            settings.file_browser_callback = function(fieldName, url, objectType, w) {
                varienGlobalEvents.fireEvent("open_browser_callback", {win:w, type:objectType, field:fieldName});
            };
        }

        if (this.config.width) {
            settings.width = this.config.width;
        }

        if (this.config.height) {
            settings.height = this.config.height;
        }

        return settings;
    },

    openFileBrowser: function(o) {
        var typeTitle;
        var wUrl = this.config.files_browser_window_url + 'target_element_id/' + this.id + '/';

        this.mediaBrowserOpener = o.win;
        this.mediaBrowserOpener.blur();

        if (typeof(o.type) != 'undefined' && o.type != "") {
            typeTitle = 'image' == o.type ? this.translate('Insert Image...') : this.translate('Insert Media...');
            wUrl = wUrl + "type/" + o.type + "/";
        } else {
            typeTitle = this.translate('Insert File...');
        }

        MediabrowserUtility.openDialog(wUrl, this.config.files_browser_window_width, this.config.files_browser_window_height, typeTitle);
    },

    translate: function(string) {
        return 'undefined' != typeof(Translator) ? Translator.translate(string) : string;
    },

    getMediaBrowserOpener: function() {
        return this.mediaBrowserOpener;
    },

    getToggleButton: function() {
        return $('toggle' + this.id);
    },

    getPluginButtons: function() {
        return $$('#buttons' + this.id + ' > button.plugin');
    },

    turnOn: function() {
        this.closePopups();
        this.setup();
        tinyMCE.execCommand('mceAddControl', false, this.id);
        this.getPluginButtons().each(function(e) {
            e.hide();
        });
    },

    turnOff: function() {
        this.closePopups();
        tinyMCE.execCommand('mceRemoveControl', false, this.id);
        this.getPluginButtons().each(function(e) {
            e.show();
        });
    },

    closePopups: function() {
        // close all popups to avoid problems with updating parent content area
        closeEditorPopup('widget_window' + this.id);
        closeEditorPopup('browser_window' + this.id);
    },

    toggle: function() {
        if (!tinyMCE.get(this.id)) {
            this.turnOn();
            return true;
        } else {
            this.turnOff();
            return false;
        }
    },

    onFormValidation: function() {
        if (tinyMCE.get(this.id)) {
            $(this.id).value = tinyMCE.get(this.id).getContent();
        }
    },

    onChangeContent: function() {
        // Add "changed" to tab class if it exists
        if(this.config.tab_id) {
            var tab = $$('a[id$=' + this.config.tab_id + ']')[0];
            if ($(tab) != undefined && $(tab).hasClassName('tab-item-link')) {
                $(tab).addClassName('changed');
            }
        }
    },

    encodeDirectives: function(content) {
        // collect all HTML tags with attributes that contain directives
        return content.gsub(/<([a-z0-9\-\_]+.+?)([a-z0-9\-\_]+=["']\{\{.+?\}\}.*?["'].+?)>/i, function(match) {
            var attributesString = match[2];
            // process tag attributes string
            attributesString = attributesString.gsub(/([a-z0-9\-\_]+)=["'](\{\{.+?\}\})(.*?)["']/i, function(m) {
                // include server URL only for images src to avoid unnecessary requests
                var url = m[1].toLowerCase() == 'src' ? this.config.directives_url : '';
                return m[1] + '="' + url + '___directive/' + Base64.mageEncode(m[2]) + '/' + m[3] + '"';
            }.bind(this));

            return '<' + match[1] + attributesString + '>';

        }.bind(this));
    },

    encodeWidgets: function(content) {
        return content.gsub(/\{\{widget(.*?)\}\}/i, function(match){
            var attributes = this.parseAttributesString(match[1]);
            if (attributes.type) {
                var placeholderFilename = attributes.type.replace(/\//g, "__") + ".gif";
                if (!this.widgetPlaceholderExist(placeholderFilename)) {
                    placeholderFilename = 'default.gif';
                }
                var imageSrc = this.config.widget_images_url + placeholderFilename;
                var imageHtml = '<img';
                    imageHtml+= ' id="' + Base64.idEncode(match[0]) + '"';
                    imageHtml+= ' src="' + imageSrc + '"';
                    imageHtml+= ' title="' + match[0].replace(/\{\{/g, '{').replace(/\}\}/g, '}').replace(/\"/g, '&quot;') + '"';
                    imageHtml+= '>';

                return imageHtml;
            }
        }.bind(this));
    },

    decodeDirectives: function(content) {
        return content.gsub(/([a-z0-9\-\_]+)=["]\S*?___directive\/([a-zA-Z0-9\-\_\,]+)\/(.*?)["]/i, function(match) {
            return match[1] + '="' + Base64.mageDecode(match[2]) + '"';
        }.bind(this));
    },

    decodeWidgets: function(content) {
        return content.gsub(/<img([^>]+id=\"[^>]+)>/i, function(match) {
            var attributes = this.parseAttributesString(match[1]);
            if(attributes.id) {
                var widgetCode = Base64.idDecode(attributes.id);
                if (widgetCode.indexOf('{{widget') != -1) {
                    return widgetCode;
                }
                return match[0];
            }
            return match[0];
        }.bind(this));
    },

    parseAttributesString: function(attributes) {
        var result = {};
        attributes.gsub(/(\w+)(?:\s*=\s*(?:(?:"((?:\\.|[^"])*)")|(?:'((?:\\.|[^'])*)')|([^>\s]+)))?/, function(match){
            result[match[1]] = match[2];
        });
        return result;
    },

    beforeSetContent: function(o) {
        if(this.config.add_widgets) {
            o.content = this.encodeWidgets(o.content);
            o.content = this.encodeDirectives(o.content);
        }
    },

    saveContent: function(o) {
        if(this.config.add_widgets) {
            o.content = this.decodeWidgets(o.content);
            o.content = this.decodeDirectives(o.content);
        }
    },

    widgetPlaceholderExist: function(filename) {
        return this.config.widget_placeholders.indexOf(filename) != -1;
    }
}

