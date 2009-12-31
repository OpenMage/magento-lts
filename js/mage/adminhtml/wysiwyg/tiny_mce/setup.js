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
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

var tinyMceWysiwygSetup = Class.create();
tinyMceWysiwygSetup.prototype =
{
    initialize: function(htmlId, config)
    {
        this.id = htmlId;
        this.config = config;
        varienGlobalEvents.attachEventHandler('tinymceChange', this.onChangeContent.bind(this));
        this.notifyFirebug();
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

    setup: function()
    {
        if (this.config.widget_plugin_src) {
            tinymce.PluginManager.load('magentowidget', this.config.widget_plugin_src);
        }
        tinyMCE.init(this.getSettings());
    },

    getSettings: function()
    {
        var plugins = 'safari,pagebreak,style,layer,table,advhr,advimage,emotions,iespell,media,searchreplace,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras';

        if (this.config.widget_plugin_src) {
            plugins = '-magentowidget,' + plugins;
        }

        var settings = {
            mode : 'exact',
            elements : this.id,
            theme : 'advanced',
            plugins : plugins,
            theme_advanced_buttons1 : 'magentowidget,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect',
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
            settings.file_browser_callback = 'imagebrowser';
        }

        return settings;
    },

    openImagesBrowser: function(o) {
        var win = o.win;
        var type = o.type;
        var field = o.field;
        var wWidth = this.config.files_browser_window_width;
        var wHeight = this.config.files_browser_window_height;
        var wUrl = this.config.files_browser_window_url;
        if (type != undefined && type != "") {
            wUrl = wUrl + "type/" + type + "/";
        }
        openEditorPopup(wUrl, 'browser_window' + this.id, 'width=' + wWidth + ', height=' + wHeight, win);
    },

    toggle: function() {
        this.toggleEditorControl();

        $$('#buttons' + this.id + ' > button.plugin').each(function(e) {
            e.toggle();
        });
    },

    toggleEditorControl: function() {
        // close all popups to avoid problems with updating parent content area
        closeEditorPopup('widget_window' + this.id);
        closeEditorPopup('browser_window' + this.id);

        if (!tinyMCE.get(this.id)) {
            this.setup();
            setTimeout('',1000);
            tinyMCE.execCommand('mceAddControl', false, this.id);
            return true;
        } else {
            tinyMCE.execCommand('mceRemoveControl', false, this.id);
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
        return content.gsub(/(src|href)\s*\=\s*[\"\']{1}(\{\{[a-z]{0,10}.*?\}\})[\"\']{1}/i, function(match){
            return match[1] + '="' + this.config.directives_url + 'directive/' + Base64.mageEncode(match[2]) + '/"';
        }.bind(this));
    },

	encodeWidgets: function(content) {
        return content.gsub(/\{\{widget(.*?)\}\}/i, function(match){
            var attributes = this.parseAttributesString(match[1]);
            var placeholderFilename = attributes.type.replace(/\//g, "__") + ".gif";
            if(!this.widgetPlaceholderExist(placeholderFilename)) {
                placeholderFilename = 'default.gif';
            }
            var imageSrc = this.config.widget_images_url + placeholderFilename;

            var imageHtml = '<img';
                imageHtml+= ' id="' + Base64.idEncode(match[0]) + '"';
                imageHtml+= ' src="' + imageSrc + '"';
                imageHtml+= ' class="widget"';
                imageHtml+= ' title="' + match[0].replace(/\{\{/g, '{').replace(/\}\}/g, '}').replace(/\"/g, '&quot;') + '"';
                imageHtml+= '>';

            return imageHtml;

        }.bind(this));
    },

    decodeDirectives: function(content) {
        var reg = new RegExp(this.config.directives_url_quoted + 'directive\/([a-zA-Z0-9\-\_\,]+)\/?', 'i');
        return content.gsub(reg, function(match){
            return Base64.mageDecode(match[1]);
        }.bind(this));
    },

    decodeWidgets: function(content) {
        return content.gsub(/<img([^>]+class=\"widget\"[^>]*)>/i, function(match) {
            var attributes = this.parseAttributesString(match[1]);
            if(attributes.id) {
                return Base64.idDecode(attributes.id);
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
        o.content = this.encodeWidgets(o.content);
        o.content = this.encodeDirectives(o.content);
    },

    saveContent: function(o) {
        o.content = this.decodeWidgets(o.content);
        o.content = this.decodeDirectives(o.content);
    },

    widgetPlaceholderExist: function(filename) {
        return this.config.widget_placeholders.indexOf(filename) != -1;
    }
}
