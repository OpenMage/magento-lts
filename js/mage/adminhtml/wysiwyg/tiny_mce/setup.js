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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

var tinyMceWysiwygSetup = Class.create();
tinyMceWysiwygSetup.prototype =
{
    mediaBrowserOpener: null,
    mediaBrowserTargetElementId: null,

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
        var plugins = 'inlinepopups,safari,pagebreak,style,layer,table,advhr,advimage,emotions,iespell,media,searchreplace,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras';

        if (this.config.widget_plugin_src) {
            plugins = 'magentowidget,' + plugins;
        }

        var magentoPluginsOptions = $H({});
        var magentoPlugins = '';

        if (this.config.plugins) {
            (this.config.plugins).each(function(plugin){
                magentoPlugins = plugin.name + ',' + magentoPlugins;
                magentoPluginsOptions.set(plugin.name, plugin.options);
            });
            if (magentoPlugins) {
                plugins = '-' + magentoPlugins + plugins;
            }
        }

        var settings = {
            schema : 'html5',
            valid_elements : ""
                +"a[accesskey|charset|class|coords|dir<ltr?rtl|href|hreflang|id|lang|name"
                +"|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup"
                +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|rel|rev"
                +"|shape<circle?default?poly?rect|style|tabindex|title|target|type],"
                +"abbr[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
                +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
                +"|title],"
                +"acronym[class|dir<ltr?rtl|id|id|lang|onclick|ondblclick|onkeydown|onkeypress"
                +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
                +"|title],"
                +"address[class|align|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
                +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
                +"|onmouseup|style|title],"
                +"applet[align<bottom?left?middle?right?top|alt|archive|class|code|codebase"
                +"|height|hspace|id|name|object|style|title|vspace|width],"
                +"area[accesskey|alt|class|coords|dir<ltr?rtl|href|id|lang|nohref<nohref"
                +"|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup"
                +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup"
                +"|shape<circle?default?poly?rect|style|tabindex|title|target],"
                +"base[href|target],"
                +"basefont[color|face|id|size],"
                +"bdo[class|dir<ltr?rtl|id|lang|style|title],"
                +"big[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
                +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
                +"|title],"
                +"blockquote[cite|class|dir<ltr?rtl|id|lang|onclick|ondblclick"
                +"|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout"
                +"|onmouseover|onmouseup|style|title],"
                +"body[alink|background|bgcolor|class|dir<ltr?rtl|id|lang|link|onclick"
                +"|ondblclick|onkeydown|onkeypress|onkeyup|onload|onmousedown|onmousemove"
                +"|onmouseout|onmouseover|onmouseup|onunload|style|title|text|vlink],"
                +"br[class|clear<all?left?none?right|id|style|title],"
                +"button[accesskey|class|dir<ltr?rtl|disabled<disabled|id|lang|name|onblur"
                +"|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup|onmousedown"
                +"|onmousemove|onmouseout|onmouseover|onmouseup|style|tabindex|title|type"
                +"|value],"
                +"caption[align<bottom?left?right?top|class|dir<ltr?rtl|id|lang|onclick"
                +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
                +"|onmouseout|onmouseover|onmouseup|style|title],"
                +"center[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
                +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
                +"|title],"
                +"cite[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
                +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
                +"|title],"
                +"code[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
                +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
                +"|title],"
                +"col[align<center?char?justify?left?right|char|charoff|class|dir<ltr?rtl|id"
                +"|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown"
                +"|onmousemove|onmouseout|onmouseover|onmouseup|span|style|title"
                +"|valign<baseline?bottom?middle?top|width],"
                +"colgroup[align<center?char?justify?left?right|char|charoff|class|dir<ltr?rtl"
                +"|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown"
                +"|onmousemove|onmouseout|onmouseover|onmouseup|span|style|title"
                +"|valign<baseline?bottom?middle?top|width],"
                +"dd[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
                +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title],"
                +"del[cite|class|datetime|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
                +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
                +"|onmouseup|style|title],"
                +"dfn[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
                +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
                +"|title],"
                +"dir[class|compact<compact|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
                +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
                +"|onmouseup|style|title],"
                +"div[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
                +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
                +"|onmouseout|onmouseover|onmouseup|style|title],"
                +"dl[class|compact<compact|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
                +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
                +"|onmouseup|style|title],"
                +"dt[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
                +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title],"
                +"em/i[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
                +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
                +"|title],"
                +"fieldset[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
                +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
                +"|title],"
                +"font[class|color|dir<ltr?rtl|face|id|lang|size|style|title],"
                +"form[accept|accept-charset|action|class|dir<ltr?rtl|enctype|id|lang"
                +"|method<get?post|name|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
                +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onreset|onsubmit"
                +"|style|title|target],"
                +"frame[class|frameborder|id|longdesc|marginheight|marginwidth|name"
                +"|noresize<noresize|scrolling<auto?no?yes|src|style|title],"
                +"frameset[class|cols|id|onload|onunload|rows|style|title],"
                +"h1[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
                +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
                +"|onmouseout|onmouseover|onmouseup|style|title],"
                +"h2[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
                +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
                +"|onmouseout|onmouseover|onmouseup|style|title],"
                +"h3[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
                +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
                +"|onmouseout|onmouseover|onmouseup|style|title],"
                +"h4[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
                +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
                +"|onmouseout|onmouseover|onmouseup|style|title],"
                +"h5[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
                +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
                +"|onmouseout|onmouseover|onmouseup|style|title],"
                +"h6[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
                +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
                +"|onmouseout|onmouseover|onmouseup|style|title],"
                +"head[dir<ltr?rtl|lang|profile],"
                +"hr[align<center?left?right|class|dir<ltr?rtl|id|lang|noshade<noshade|onclick"
                +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
                +"|onmouseout|onmouseover|onmouseup|size|style|title|width],"
                +"html[dir<ltr?rtl|lang|version],"
                +"iframe[align<bottom?left?middle?right?top|class|frameborder|height|id"
                +"|longdesc|marginheight|marginwidth|name|scrolling<auto?no?yes|src|style"
                +"|title|width],"
                +"img[align<bottom?left?middle?right?top|alt|border|class|dir<ltr?rtl|height"
                +"|hspace|id|ismap<ismap|lang|longdesc|name|onclick|ondblclick|onkeydown"
                +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
                +"|onmouseup|src|style|title|usemap|vspace|width],"
                +"input[accept|accesskey|align<bottom?left?middle?right?top|alt"
                +"|checked<checked|class|dir<ltr?rtl|disabled<disabled|id|ismap<ismap|lang"
                +"|maxlength|name|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress"
                +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onselect"
                +"|readonly<readonly|size|src|style|tabindex|title"
                +"|type<button?checkbox?file?hidden?image?password?radio?reset?submit?text"
                +"|usemap|value],"
                +"ins[cite|class|datetime|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
                +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
                +"|onmouseup|style|title],"
                +"isindex[class|dir<ltr?rtl|id|lang|prompt|style|title],"
                +"kbd[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
                +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
                +"|title],"
                +"label[accesskey|class|dir<ltr?rtl|for|id|lang|onblur|onclick|ondblclick"
                +"|onfocus|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout"
                +"|onmouseover|onmouseup|style|title],"
                +"legend[align<bottom?left?right?top|accesskey|class|dir<ltr?rtl|id|lang"
                +"|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
                +"|onmouseout|onmouseover|onmouseup|style|title],"
                +"li[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
                +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title|type"
                +"|value],"
                +"link[charset|class|dir<ltr?rtl|href|hreflang|id|lang|media|onclick"
                +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
                +"|onmouseout|onmouseover|onmouseup|rel|rev|style|title|target|type],"
                +"map[class|dir<ltr?rtl|id|lang|name|onclick|ondblclick|onkeydown|onkeypress"
                +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
                +"|title],"
                +"menu[class|compact<compact|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
                +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
                +"|onmouseup|style|title],"
                +"meta[content|dir<ltr?rtl|http-equiv|lang|name|scheme],"
                +"noframes[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
                +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
                +"|title],"
                +"noscript[class|dir<ltr?rtl|id|lang|style|title],"
                +"object[align<bottom?left?middle?right?top|archive|border|class|classid"
                +"|codebase|codetype|data|declare|dir<ltr?rtl|height|hspace|id|lang|name"
                +"|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
                +"|onmouseout|onmouseover|onmouseup|standby|style|tabindex|title|type|usemap"
                +"|vspace|width],"
                +"ol[class|compact<compact|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
                +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
                +"|onmouseup|start|style|title|type],"
                +"optgroup[class|dir<ltr?rtl|disabled<disabled|id|label|lang|onclick"
                +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
                +"|onmouseout|onmouseover|onmouseup|style|title],"
                +"option[class|dir<ltr?rtl|disabled<disabled|id|label|lang|onclick|ondblclick"
                +"|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout"
                +"|onmouseover|onmouseup|selected<selected|style|title|value],"
                +"p[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
                +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
                +"|onmouseout|onmouseover|onmouseup|style|title],"
                +"param[id|name|type|value|valuetype<DATA?OBJECT?REF],"
                +"pre/listing/plaintext/xmp[align|class|dir<ltr?rtl|id|lang|onclick|ondblclick"
                +"|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout"
                +"|onmouseover|onmouseup|style|title|width],"
                +"q[cite|class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
                +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
                +"|title],"
                +"s[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
                +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title],"
                +"samp[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
                +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
                +"|title],"
                +"script[charset|defer|language|src|type],"
                +"select[class|dir<ltr?rtl|disabled<disabled|id|lang|multiple<multiple|name"
                +"|onblur|onchange|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup"
                +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|size|style"
                +"|tabindex|title],"
                +"small[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
                +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
                +"|title],"
                +"span[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
                +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
                +"|onmouseup|style|title],"
                +"strike[class|class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
                +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
                +"|onmouseup|style|title],"
                +"strong/b[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
                +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
                +"|title],"
                +"style[dir<ltr?rtl|lang|media|title|type],"
                +"sub[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
                +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
                +"|title],"
                +"sup[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
                +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
                +"|title],"
                +"table[align<center?left?right|bgcolor|border|cellpadding|cellspacing|class"
                +"|dir<ltr?rtl|frame|height|id|lang|onclick|ondblclick|onkeydown|onkeypress"
                +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|rules"
                +"|style|summary|title|width],"
                +"tbody[align<center?char?justify?left?right|char|class|charoff|dir<ltr?rtl|id"
                +"|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown"
                +"|onmousemove|onmouseout|onmouseover|onmouseup|style|title"
                +"|valign<baseline?bottom?middle?top],"
                +"td[abbr|align<center?char?justify?left?right|axis|bgcolor|char|charoff|class"
                +"|colspan|dir<ltr?rtl|headers|height|id|lang|nowrap<nowrap|onclick"
                +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
                +"|onmouseout|onmouseover|onmouseup|rowspan|scope<col?colgroup?row?rowgroup"
                +"|style|title|valign<baseline?bottom?middle?top|width],"
                +"textarea[accesskey|class|cols|dir<ltr?rtl|disabled<disabled|id|lang|name"
                +"|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup"
                +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onselect"
                +"|readonly<readonly|rows|style|tabindex|title],"
                +"tfoot[align<center?char?justify?left?right|char|charoff|class|dir<ltr?rtl|id"
                +"|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown"
                +"|onmousemove|onmouseout|onmouseover|onmouseup|style|title"
                +"|valign<baseline?bottom?middle?top],"
                +"th[abbr|align<center?char?justify?left?right|axis|bgcolor|char|charoff|class"
                +"|colspan|dir<ltr?rtl|headers|height|id|lang|nowrap<nowrap|onclick"
                +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
                +"|onmouseout|onmouseover|onmouseup|rowspan|scope<col?colgroup?row?rowgroup"
                +"|style|title|valign<baseline?bottom?middle?top|width],"
                +"thead[align<center?char?justify?left?right|char|charoff|class|dir<ltr?rtl|id"
                +"|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown"
                +"|onmousemove|onmouseout|onmouseover|onmouseup|style|title"
                +"|valign<baseline?bottom?middle?top],"
                +"title[dir<ltr?rtl|lang],"
                +"tr[abbr|align<center?char?justify?left?right|bgcolor|char|charoff|class"
                +"|rowspan|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
                +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
                +"|title|valign<baseline?bottom?middle?top],"
                +"tt[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
                +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title],"
                +"u[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
                +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title],"
                +"ul[class|compact<compact|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
                +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
                +"|onmouseup|style|title|type],"
                +"var[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
                +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
                +"|title]",
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
            media_disable_flash : this.config.media_disable_flash,
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

        // Set the document base URL
        if (this.config.document_base_url) {
            settings.document_base_url = this.config.document_base_url;
        }

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
        var storeId = this.config.store_id !== null ? this.config.store_id : 0;
        var wUrl = this.config.files_browser_window_url +
                   'target_element_id/' + this.id + '/' +
                   'store/' + storeId + '/';

        this.mediaBrowserOpener = o.win;
        this.mediaBrowserTargetElementId = o.field;

        if (typeof(o.type) != 'undefined' && o.type != "") {
            typeTitle = 'image' == o.type ? this.translate('Insert Image...') : this.translate('Insert Media...');
            wUrl = wUrl + "type/" + o.type + "/";
        } else {
            typeTitle = this.translate('Insert File...');
        }

        MediabrowserUtility.openDialog(wUrl, false, false, typeTitle, {
            onBeforeShow: function(win) {
                win.element.setStyle({zIndex: 300200});
            }
        });
    },

    translate: function(string) {
        return 'undefined' != typeof(Translator) ? Translator.translate(string) : string;
    },

    getMediaBrowserOpener: function() {
        return this.mediaBrowserOpener;
    },

    getMediaBrowserTargetElementId: function() {
        return this.mediaBrowserTargetElementId;
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
        if (Prototype.Browser.IE) {
            // workaround for IE textarea redraw bug
            window.setTimeout(function() {
                if ($(this.id)) {
                    $(this.id).value = $(this.id).value;
                }
            }.bind(this), 0);
        }
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

    // retrieve directives URL with substituted directive value
    makeDirectiveUrl: function(directive) {
        return this.config.directives_url.replace('directive', 'directive/___directive/' + directive);
    },

    encodeDirectives: function(content) {
        // collect all HTML tags with attributes that contain directives
        return content.gsub(/<([a-z0-9\-\_]+.+?)([a-z0-9\-\_]+=".*?\{\{.+?\}\}.*?".+?)>/i, function(match) {
            var attributesString = match[2];
            // process tag attributes string
            attributesString = attributesString.gsub(/([a-z0-9\-\_]+)="(.*?)(\{\{.+?\}\})(.*?)"/i, function(m) {
                return m[1] + '="' + m[2] + this.makeDirectiveUrl(Base64.mageEncode(m[3])) + m[4] + '"';
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
        // escape special chars in directives url to use it in regular expression
        var url = this.makeDirectiveUrl('%directive%').replace(/([$^.?*!+:=()\[\]{}|\\])/g, '\\$1');
        var reg = new RegExp(url.replace('%directive%', '([a-zA-Z0-9,_-]+)'));
        return content.gsub(reg, function(match) {
            return Base64.mageDecode(match[1]);
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
        if (this.config.add_widgets) {
            o.content = this.encodeWidgets(o.content);
            o.content = this.encodeDirectives(o.content);
        } else if (this.config.add_directives) {
            o.content = this.encodeDirectives(o.content);
        }
    },

    saveContent: function(o) {
        if (this.config.add_widgets) {
            o.content = this.decodeWidgets(o.content);
            o.content = this.decodeDirectives(o.content);
        } else if (this.config.add_directives) {
            o.content = this.decodeDirectives(o.content);
        }
    },

    widgetPlaceholderExist: function(filename) {
        return this.config.widget_placeholders.indexOf(filename) != -1;
    }
};
