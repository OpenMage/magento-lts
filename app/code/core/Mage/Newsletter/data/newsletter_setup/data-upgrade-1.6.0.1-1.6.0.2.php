<?php
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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Newsletter
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */

$installer = $this;

$newsletterContent = <<<EOD
{{template config_path="design/email/header"}}
{{inlinecss file="email-inline.css"}}

<table cellpadding="0" cellspacing="0" border="0">
<tr>
    <td class="full">
        <table class="columns">
            <tr>
                <td class="email-heading">
                    <h1>Welcome</h1>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit,
                    sed do eiusmod tempor incididunt ut labore et.</p>
                </td>
                <td class="store-info">
                    <h4>Contact Us</h4>
                    <p>
                        {{depend store_phone}}
                        <b>Call Us:</b>
                        <a href="tel:{{var phone}}">{{var store_phone}}</a><br>
                        {{/depend}}
                        {{depend store_hours}}
                        <span class="no-link">{{var store_hours}}</span><br>
                        {{/depend}}
                        {{depend store_email}}
                        <b>Email:</b> <a href="mailto:{{var store_email}}">{{var store_email}}</a>
                        {{/depend}}
                    </p>
                </td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <td class="full">
        <table class="columns">
            <tr>
                <td>
                    <img width="600" src="http://placehold.it/600x200" class="main-image">
                </td>
                <td class="expander"></td>
            </tr>
        </table>
        <table class="columns">
            <tr>
                <td class="panel">
                    <p>Phasellus dictum sapien a neque luctus cursus. Pellentesque sem dolor, fringilla et pharetra
                    vitae. <a href="#">Click it! &raquo;</a></p>
                </td>
                <td class="expander"></td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <td>
        <table class="row">
            <tr>
                <td class="half left wrapper">
                    <table class="columns">
                        <tr>
                            <td>
                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor
                                incididunt ut labore et. Lorem ipsum dolor sit amet, consectetur adipisicing elit,
                                sed do eiusmod tempor incididunt ut labore et. Lorem ipsum dolor sit amet.</p>
                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor
                                incididunt ut labore et. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed
                                do eiusmod tempor incididunt ut labore et. Lorem ipsum dolor sit amet.</p>
                                <table class="button">
                                    <tr>
                                        <td>
                                            <a href="#">Click Me!</a>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td class="expander"></td>
                        </tr>
                    </table>
                </td>
                <td class="half right wrapper last">
                    <table class="columns">
                        <tr>
                            <td class="panel sidebar-links">
                                <h6>Header Thing</h6>
                                <p>Sub-head or something</p>
                                <table>
                                    <tr>
                                        <td>
                                            <p><a href="#">Just a Plain Link &raquo;</a></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <hr/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p><a href="#">Just a Plain Link &raquo;</a></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <hr/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p><a href="#">Just a Plain Link &raquo;</a></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <hr/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p><a href="#">Just a Plain Link &raquo;</a></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <hr/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p><a href="#">Just a Plain Link &raquo;</a></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <hr/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p><a href="#">Just a Plain Link &raquo;</a></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <hr/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p><a href="#">Just a Plain Link &raquo;</a></p>
                                        </td>
                                    </tr>
                                    <tr><td>&nbsp;</td></tr>
                                </table>
                            </td>
                            <td class="expander"></td>
                        </tr>
                    </table>
                    <br>
                    <table class="columns">
                        <tr>
                            <td class="panel">
                                <h6>Connect With Us:</h6>
                                <table class="social-button facebook">
                                    <tr>
                                        <td>
                                            <a href="#">Facebook</a>
                                        </td>
                                    </tr>
                                </table>
                                <hr>
                                <table class="social-button twitter">
                                    <tr>
                                        <td>
                                            <a href="#">Twitter</a>
                                        </td>
                                    </tr>
                                </table>
                                <hr>
                                <table class="social-button google-plus">
                                    <tr>
                                        <td>
                                            <a href="#">Google +</a>
                                        </td>
                                    </tr>
                                </table>
                                <br>
                                <h6>Contact Info:</h6>
                                {{depend store_phone}}
                                <p>
                                    <b>Call Us:</b>
                                    <a href="tel:{{var phone}}">{{var store_phone}}</a>
                                </p>
                                {{/depend}}
                                {{depend store_hours}}
                                <p><span class="no-link">{{var store_hours}}</span><br></p>
                                {{/depend}}
                                {{depend store_email}}
                                <p><b>Email:</b> <a href="mailto:{{var store_email}}">{{var store_email}}</a></p>
                                {{/depend}}
                            </td>
                            <td class="expander"></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table class="row">
            <tr>
                <td class="full wrapper">
                    {{block type="catalog/product_new" template="email/catalog/product/new.phtml" products_count="4"
                    column_count="4" }}
                </td>
            </tr>
        </table>
        <table class="row">
            <tr>
                <td class="full wrapper last">
                    <table class="columns">
                        <tr>
                            <td align="center">
                                <center>
                                    <p><a href="#">Terms</a> | <a href="#">Privacy</a> | <a href="#">Unsubscribe</a></p>
                                </center>
                            </td>
                            <td class="expander"></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </td>
</tr>
</table>

{{template config_path="design/email/footer"}}
EOD;

$data = array(
    'template_code' => 'Example Newsletter Template',
    'template_text' => $newsletterContent ,
    'template_styles' => NULL,
    'template_type' => Mage_Newsletter_Model_Template::TYPE_HTML,
    'template_subject' => 'Example Subject',
    'template_sender_name' => Mage::getStoreConfig('trans_email/ident_general/name'),
    'template_sender_email' => Mage::getStoreConfig('trans_email/ident_general/email'),
    'template_actual' => 1,
    'added_at' => Mage::getSingleton('core/date')->gmtDate(),
    'modified_at' => Mage::getSingleton('core/date')->gmtDate()
);

$model = Mage::getModel('newsletter/template')->setData($data);

try {
    $model->save();
} catch (Exception $e){
    Mage::logException($e->getMessage());
}
