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
 * @package     Mage_Cms
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


$pageContent = <<<EOD
<p style="color: #ff0000; font-weight: bold; font-size: 13px">
    Please replace this text with you Privacy Policy.
    Please add any additional cookies your website uses below (e.g., Google Analytics)
</p>
<p>
    This privacy policy sets out how {{config path="general/store_information/name"}} uses and protects any information
    that you give {{config path="general/store_information/name"}} when you use this website.
    {{config path="general/store_information/name"}} is committed to ensuring that your privacy is protected.
    Should we ask you to provide certain information by which you can be identified when using this website,
    then you can be assured that it will only be used in accordance with this privacy statement.
    {{config path="general/store_information/name"}} may change this policy from time to time by updating this page.
    You should check this page from time to time to ensure that you are happy with any changes.
</p>
<h2>What we collect</h2>
<p>We may collect the following information:</p>
<ul>
    <li>name</li>
    <li>contact information including email address</li>
    <li>demographic information such as postcode, preferences and interests</li>
    <li>other information relevant to customer surveys and/or offers</li>
</ul>
<p>
    For the exhaustive list of cookies we collect see the <a href="#list">List of cookies we collect</a> section.
</p>
<h2>What we do with the information we gather</h2>
<p>
    We require this information to understand your needs and provide you with a better service,
    and in particular for the following reasons:
</p>
<ul>
    <li>Internal record keeping.</li>
    <li>We may use the information to improve our products and services.</li>
    <li>
        We may periodically send promotional emails about new products, special offers or other information which we
        think you may find interesting using the email address which you have provided.
    </li>
    <li>
        From time to time, we may also use your information to contact you for market research purposes.
        We may contact you by email, phone, fax or mail. We may use the information to customise the website
        according to your interests.
    </li>
</ul>
<h2>Security</h2>
<p>
    We are committed to ensuring that your information is secure. In order to prevent unauthorised access or disclosure,
    we have put in place suitable physical, electronic and managerial procedures to safeguard and secure
    the information we collect online.
</p>
<h2>How we use cookies</h2>
<p>
    A cookie is a small file which asks permission to be placed on your computer's hard drive.
    Once you agree, the file is added and the cookie helps analyse web traffic or lets you know when you visit
    a particular site. Cookies allow web applications to respond to you as an individual. The web application
    can tailor its operations to your needs, likes and dislikes by gathering and remembering information about
    your preferences.
</p>
<p>
    We use traffic log cookies to identify which pages are being used. This helps us analyse data about web page traffic
    and improve our website in order to tailor it to customer needs. We only use this information for statistical
    analysis purposes and then the data is removed from the system.
</p>
<p>
    Overall, cookies help us provide you with a better website, by enabling us to monitor which pages you find useful
    and which you do not. A cookie in no way gives us access to your computer or any information about you,
    other than the data you choose to share with us. You can choose to accept or decline cookies.
    Most web browsers automatically accept cookies, but you can usually modify your browser setting
    to decline cookies if you prefer. This may prevent you from taking full advantage of the website.
</p>
<h2>Links to other websites</h2>
<p>
    Our website may contain links to other websites of interest. However, once you have used these links
    to leave our site, you should note that we do not have any control over that other website.
    Therefore, we cannot be responsible for the protection and privacy of any information which you provide whilst
    visiting such sites and such sites are not governed by this privacy statement.
    You should exercise caution and look at the privacy statement applicable to the website in question.
</p>
<h2>Controlling your personal information</h2>
<p>You may choose to restrict the collection or use of your personal information in the following ways:</p>
<ul>
    <li>
        whenever you are asked to fill in a form on the website, look for the box that you can click to indicate
        that you do not want the information to be used by anybody for direct marketing purposes
    </li>
    <li>
        if you have previously agreed to us using your personal information for direct marketing purposes,
        you may change your mind at any time by writing to or emailing us at
        {{config path="trans_email/ident_general/email"}}
    </li>
</ul>
<p>
    We will not sell, distribute or lease your personal information to third parties unless we have your permission
    or are required by law to do so. We may use your personal information to send you promotional information
    about third parties which we think you may find interesting if you tell us that you wish this to happen.
</p>
<p>
    You may request details of personal information which we hold about you under the Data Protection Act 1998.
    A small fee will be payable. If you would like a copy of the information held on you please write to
    {{config path="general/store_information/address"}}.
</p>
<p>
    If you believe that any information we are holding on you is incorrect or incomplete,
    please write to or email us as soon as possible, at the above address.
    We will promptly correct any information found to be incorrect.
</p>
<h2><a name="list"></a>List of cookies we collect</h2>
<p>The table below lists the cookies we collect and what information they store.</p>
<table class="data-table">
    <thead>
        <tr>
            <th>COOKIE name</th>
            <th>COOKIE Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th>CART</th>
            <td>The association with your shopping cart.</td>
        </tr>
        <tr>
            <th>CATEGORY_INFO</th>
            <td>Stores the category info on the page, that allows to display pages more quickly.</td>
        </tr>
        <tr>
            <th>COMPARE</th>
            <td>The items that you have in the Compare Products list.</td>
        </tr>
        <tr>
            <th>CURRENCY</th>
            <td>Your preferred currency</td>
        </tr>
        <tr>
            <th>CUSTOMER</th>
            <td>An encrypted version of your customer id with the store.</td>
        </tr>
        <tr>
            <th>CUSTOMER_AUTH</th>
            <td>An indicator if you are currently logged into the store.</td>
        </tr>
        <tr>
            <th>CUSTOMER_INFO</th>
            <td>An encrypted version of the customer group you belong to.</td>
        </tr>
        <tr>
            <th>CUSTOMER_SEGMENT_IDS</th>
            <td>Stores the Customer Segment ID</td>
        </tr>
        <tr>
            <th>EXTERNAL_NO_CACHE</th>
            <td>A flag, which indicates whether caching is disabled or not.</td>
        </tr>
        <tr>
            <th>FRONTEND</th>
            <td>You sesssion ID on the server.</td>
        </tr>
        <tr>
            <th>GUEST-VIEW</th>
            <td>Allows guests to edit their orders.</td>
        </tr>
        <tr>
            <th>LAST_CATEGORY</th>
            <td>The last category you visited.</td>
        </tr>
        <tr>
            <th>LAST_PRODUCT</th>
            <td>The most recent product you have viewed.</td>
        </tr>
        <tr>
            <th>NEWMESSAGE</th>
            <td>Indicates whether a new message has been received.</td>
        </tr>
        <tr>
            <th>NO_CACHE</th>
            <td>Indicates whether it is allowed to use cache.</td>
        </tr>
        <tr>
            <th>PERSISTENT_SHOPPING_CART</th>
            <td>A link to information about your cart and viewing history if you have asked the site.</td>
        </tr>
        <tr>
            <th>POLL</th>
            <td>The ID of any polls you have recently voted in.</td>
        </tr>
        <tr>
            <th>POLLN</th>
            <td>Information on what polls you have voted on.</td>
        </tr>
        <tr>
            <th>RECENTLYCOMPARED</th>
            <td>The items that you have recently compared.            </td>
        </tr>
        <tr>
            <th>STF</th>
            <td>Information on products you have emailed to friends.</td>
        </tr>
        <tr>
            <th>STORE</th>
            <td>The store view or language you have selected.</td>
        </tr>
        <tr>
            <th>USER_ALLOWED_SAVE_COOKIE</th>
            <td>Indicates whether a customer allowed to use cookies.</td>
        </tr>
        <tr>
            <th>VIEWED_PRODUCT_IDS</th>
            <td>The products that you have recently viewed.</td>
        </tr>
        <tr>
            <th>WISHLIST</th>
            <td>An encrypted list of products added to your Wishlist.</td>
        </tr>
        <tr>
            <th>WISHLIST_CNT</th>
            <td>The number of items in your Wishlist.</td>
        </tr>
    </tbody>
</table>
EOD;

$privacyPageData = array(
    'title'           => 'Privacy Policy',
    'content_heading' => 'Privacy Policy',
    'root_template'   => 'one_column',
    'identifier'      => 'privacy-policy-cookie-restriction-mode',
    'content'         => $pageContent,
    'is_active'       => 1,
    'stores'          => array(0),
    'sort_order'      => 0
);

Mage::getModel('cms/page')->setData($privacyPageData)->save();

$footerLinksBlock = Mage::getModel('cms/block')->load('footer_links','identifier');

if ($footerLinksBlock->getId()) {
    $content = $footerLinksBlock->getContent();
    if (preg_match('/<ul>(.*?)<\\/ul>/ims',$content, $matches)) {
        $content = preg_replace('/<li class="last">/ims', '<li>',$content);
        $replacment = '<li class="last privacy">'
            . "<a href=\"{{store direct_url=\"privacy-policy-cookie-restriction-mode\"}}\">"
            . "Privacy Policy</a></li>\r\n</ul>";
        $content = preg_replace('/<\\/ul>/ims', $replacment, $content);
        $footerLinksBlock->setContent($content)->save();
    }
}
