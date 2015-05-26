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

$cmsBlocks = array(
    array(
        'title'         => 'Footer Links',
        'identifier'    => 'footer_links',
        'content'       => "
<ul>
    <li><a href=\"{{store direct_url=\"about-magento-demo-store\"}}\">About Us</a></li>
    <li class=\"last\"><a href=\"{{store direct_url=\"customer-service\"}}\">Customer Service</a></li>
</ul>",
        'is_active'     => 1,
        'stores'        => 0
    )
);

$cmsPages = array(
    array(
        'title'         => '404 Not Found 1',
        'root_template' => 'two_columns_right',
        'meta_keywords' => 'Page keywords',
        'meta_description'
                        => 'Page description',
        'identifier'    => 'no-route',
        'content'       => "
<div class=\"page-title\"><h1>Whoops, our bad...</h1></div>
<dl>
    <dt>The page you requested was not found, and we have a fine guess why.</dt>
    <dd>
        <ul class=\"disc\">
            <li>If you typed the URL directly, please make sure the spelling is correct.</li>
            <li>If you clicked on a link to get here, the link is outdated.</li>
        </ul>
    </dd>
</dl>
<dl>
    <dt>What can you do?</dt>
    <dd>Have no fear, help is near! There are many ways you can get back on track with Magento Store.</dd>
    <dd>
        <ul class=\"disc\">
            <li><a href=\"#\" onclick=\"history.go(-1); return false;\">Go back</a> to the previous page.</li>
            <li>Use the search bar at the top of the page to search for your products.</li>
            <li>Follow these links to get you back on track!<br /><a href=\"{{store url=\"\"}}\">Store Home</a>
            <span class=\"separator\">|</span> <a href=\"{{store url=\"customer/account\"}}\">My Account</a></li>
        </ul>
    </dd>
</dl>
",
        'is_active'     => 1,
        'stores'        => array(0),
        'sort_order'    => 0
    ),
    array(
        'title'         => 'Home page',
        'root_template' => 'two_columns_right',
        'identifier'    => 'home',
        'content'       => "<div class=\"page-title\"><h2>Home Page</h2></div>",
        'is_active'     => 1,
        'stores'        => array(0),
        'sort_order'    => 0
    ),
    array(
        'title'         => 'About Us',
        'root_template' => 'two_columns_right',
        'identifier'    => 'about-magento-demo-store',
        'content'       => "
<div class=\"page-title\">
    <h1>About Magento Store</h1>
</div>
<div class=\"col3-set\">
<div class=\"col-1\"><p style=\"line-height:1.2em;\"><small>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.
Morbi luctus. Duis lobortis. Nulla nec velit. Mauris pulvinar erat non massa. Suspendisse tortor turpis, porta nec,
tempus vitae, iaculis semper, pede.</small></p>
<p style=\"color:#888; font:1.2em/1.4em georgia, serif;\">Lorem ipsum dolor sit amet, consectetuer adipiscing elit.
Morbi luctus. Duis lobortis. Nulla nec velit. Mauris pulvinar erat non massa. Suspendisse tortor turpis,
porta nec, tempus vitae, iaculis semper, pede. Cras vel libero id lectus rhoncus porta.</p></div>
<div class=\"col-2\">
<p><strong style=\"color:#de036f;\">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Morbi luctus.
Duis lobortis. Nulla nec velit.</strong></p>
<p>Vivamus tortor nisl, lobortis in, faucibus et, tempus at, dui. Nunc risus. Proin scelerisque augue. Nam ullamcorper.
Phasellus id massa. Pellentesque nisl. Pellentesque habitant morbi tristique senectus et netus et malesuada
fames ac turpis egestas. Nunc augue. Aenean sed justo non leo vehicula laoreet. Praesent ipsum libero, auctor ac,
tempus nec, tempor nec, justo. </p>
<p>Maecenas ullamcorper, odio vel tempus egestas, dui orci faucibus orci, sit amet aliquet lectus dolor et quam.
Pellentesque consequat luctus purus. Nunc et risus. Etiam a nibh. Phasellus dignissim metus eget nisi.
Vestibulum sapien dolor, aliquet nec, porta ac, malesuada a, libero. Praesent feugiat purus eget est.
Nulla facilisi. Vestibulum tincidunt sapien eu velit. Mauris purus. Maecenas eget mauris eu orci accumsan feugiat.
Pellentesque eget velit. Nunc tincidunt.</p></div>
<div class=\"col-3\">
<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Morbi luctus. Duis lobortis. Nulla nec velit.
Mauris pulvinar erat non massa. Suspendisse tortor turpis, porta nec, tempus vitae, iaculis semper, pede.
Cras vel libero id lectus rhoncus porta. Suspendisse convallis felis ac enim. Vivamus tortor nisl, lobortis in,
faucibus et, tempus at, dui. Nunc risus. Proin scelerisque augue. Nam ullamcorper </p>
<p><strong style=\"color:#de036f;\">Maecenas ullamcorper, odio vel tempus egestas, dui orci faucibus orci,
sit amet aliquet lectus dolor et quam. Pellentesque consequat luctus purus.</strong></p>
<p>Nunc et risus. Etiam a nibh. Phasellus dignissim metus eget nisi.</p>
<div class=\"divider\"></div>
<p>To all of you, from all of us at Magento Store - Thank you and Happy eCommerce!</p>
<p style=\"line-height:1.2em;\"><strong style=\"font:italic 2em Georgia, serif;\">John Doe</strong><br />
<small>Some important guy</small></p></div>
</div>",
        'is_active'     => 1,
        'stores'        => array(0),
        'sort_order'    => 0
    ),
    array(
        'title'         => 'Customer Service',
        'root_template' => 'three_columns',
        'identifier'    => 'customer-service',
        'content'       => "<div class=\"page-title\">
<h1>Customer Service</h1>
</div>
<ul class=\"disc\">
<li><a href=\"#answer1\">Shipping &amp; Delivery</a></li>
<li><a href=\"#answer2\">Privacy &amp; Security</a></li>
<li><a href=\"#answer3\">Returns &amp; Replacements</a></li>
<li><a href=\"#answer4\">Ordering</a></li>
<li><a href=\"#answer5\">Payment, Pricing &amp; Promotions</a></li>
<li><a href=\"#answer6\">Viewing Orders</a></li>
<li><a href=\"#answer7\">Updating Account Information</a></li>
</ul>
<dl>
<dt id=\"answer1\">Shipping &amp; Delivery</dt>
<dd>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Morbi luctus. Duis lobortis. Nulla nec velit.
Mauris pulvinar erat non massa. Suspendisse tortor turpis, porta nec, tempus vitae, iaculis semper, pede.
Cras vel libero id lectus rhoncus porta. Suspendisse convallis felis ac enim. Vivamus tortor nisl, lobortis in,
faucibus et, tempus at, dui. Nunc risus. Proin scelerisque augue. Nam ullamcorper. Phasellus id massa.
Pellentesque nisl. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.
Nunc augue. Aenean sed justo non leo vehicula laoreet. Praesent ipsum libero, auctor ac, tempus nec, tempor nec,
justo.</dd>
<dt id=\"answer2\">Privacy &amp; Security</dt>
<dd>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Morbi luctus. Duis lobortis. Nulla nec velit.
Mauris pulvinar erat non massa. Suspendisse tortor turpis, porta nec, tempus vitae, iaculis semper, pede.
Cras vel libero id lectus rhoncus porta. Suspendisse convallis felis ac enim. Vivamus tortor nisl, lobortis in,
faucibus et, tempus at, dui. Nunc risus. Proin scelerisque augue. Nam ullamcorper. Phasellus id massa.
Pellentesque nisl. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.
Nunc augue. Aenean sed justo non leo vehicula laoreet. Praesent ipsum libero, auctor ac, tempus nec, tempor nec,
justo.</dd>
<dt id=\"answer3\">Returns &amp; Replacements</dt>
<dd>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Morbi luctus. Duis lobortis. Nulla nec velit.
Mauris pulvinar erat non massa. Suspendisse tortor turpis, porta nec, tempus vitae, iaculis semper, pede.
Cras vel libero id lectus rhoncus porta. Suspendisse convallis felis ac enim. Vivamus tortor nisl, lobortis in,
faucibus et, tempus at, dui. Nunc risus. Proin scelerisque augue. Nam ullamcorper. Phasellus id massa.
Pellentesque nisl. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.
Nunc augue. Aenean sed justo non leo vehicula laoreet. Praesent ipsum libero, auctor ac, tempus nec, tempor nec,
justo.</dd>
<dt id=\"answer4\">Ordering</dt>
<dd>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Morbi luctus. Duis lobortis. Nulla nec velit.
Mauris pulvinar erat non massa. Suspendisse tortor turpis, porta nec, tempus vitae, iaculis semper, pede.
Cras vel libero id lectus rhoncus porta. Suspendisse convallis felis ac enim. Vivamus tortor nisl, lobortis in,
faucibus et, tempus at, dui. Nunc risus. Proin scelerisque augue. Nam ullamcorper. Phasellus id massa.
Pellentesque nisl. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.
Nunc augue. Aenean sed justo non leo vehicula laoreet. Praesent ipsum libero, auctor ac, tempus nec, tempor nec,
justo.</dd>
<dt id=\"answer5\">Payment, Pricing &amp; Promotions</dt>
<dd>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Morbi luctus. Duis lobortis. Nulla nec velit.
Mauris pulvinar erat non massa. Suspendisse tortor turpis, porta nec, tempus vitae, iaculis semper, pede.
Cras vel libero id lectus rhoncus porta. Suspendisse convallis felis ac enim. Vivamus tortor nisl, lobortis in,
faucibus et, tempus at, dui. Nunc risus. Proin scelerisque augue. Nam ullamcorper. Phasellus id massa.
Pellentesque nisl. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.
Nunc augue. Aenean sed justo non leo vehicula laoreet. Praesent ipsum libero, auctor ac, tempus nec, tempor nec,
justo.</dd>
<dt id=\"answer6\">Viewing Orders</dt>
<dd>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Morbi luctus. Duis lobortis. Nulla nec velit.
Mauris pulvinar erat non massa. Suspendisse tortor turpis, porta nec, tempus vitae, iaculis semper, pede.
Cras vel libero id lectus rhoncus porta. Suspendisse convallis felis ac enim. Vivamus tortor nisl, lobortis in,
faucibus et, tempus at, dui. Nunc risus. Proin scelerisque augue. Nam ullamcorper. Phasellus id massa.
 Pellentesque nisl. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.
 Nunc augue. Aenean sed justo non leo vehicula laoreet. Praesent ipsum libero, auctor ac, tempus nec, tempor nec,
 justo.</dd>
<dt id=\"answer7\">Updating Account Information</dt>
<dd>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Morbi luctus. Duis lobortis. Nulla nec velit.
 Mauris pulvinar erat non massa. Suspendisse tortor turpis, porta nec, tempus vitae, iaculis semper, pede.
 Cras vel libero id lectus rhoncus porta. Suspendisse convallis felis ac enim. Vivamus tortor nisl, lobortis in,
 faucibus et, tempus at, dui. Nunc risus. Proin scelerisque augue. Nam ullamcorper. Phasellus id massa.
 Pellentesque nisl. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.
 Nunc augue. Aenean sed justo non leo vehicula laoreet. Praesent ipsum libero, auctor ac, tempus nec, tempor nec,
 justo.</dd>
</dl>",
        'is_active'     => 1,
        'stores'        => array(0),
        'sort_order'    => 0
    ),
    array(
        'title'         => 'Enable Cookies',
        'root_template' => 'one_column',
        'identifier'    => 'enable-cookies',
        'content'       => "<div class=\"std\">
    <ul class=\"messages\">
        <li class=\"notice-msg\">
            <ul>
                <li>Please enable cookies in your web browser to continue.</li>
            </ul>
        </li>
    </ul>
    <div class=\"page-title\">
        <h1><a name=\"top\"></a>What are Cookies?</h1>
    </div>
    <p>Cookies are short pieces of data that are sent to your computer when you visit a website.
    On later visits, this data is then returned to that website. Cookies allow us to recognize you automatically
    whenever you visit our site so that we can personalize your experience and provide you with better service.
    We also use cookies (and similar browser data, such as Flash cookies) for fraud prevention and other purposes.
     If your web browser is set to refuse cookies from our website, you will not be able to complete a purchase
     or take advantage of certain features of our website, such as storing items in your Shopping Cart or
     receiving personalized recommendations. As a result, we strongly encourage you to configure your web
     browser to accept cookies from our website.</p>
    <h2 class=\"subtitle\">Enabling Cookies</h2>
    <ul class=\"disc\">
        <li><a href=\"#ie7\">Internet Explorer 7.x</a></li>
        <li><a href=\"#ie6\">Internet Explorer 6.x</a></li>
        <li><a href=\"#firefox\">Mozilla/Firefox</a></li>
        <li><a href=\"#opera\">Opera 7.x</a></li>
    </ul>
    <h3><a name=\"ie7\"></a>Internet Explorer 7.x</h3>
    <ol>
        <li>
            <p>Start Internet Explorer</p>
        </li>
        <li>
            <p>Under the <strong>Tools</strong> menu, click <strong>Internet Options</strong></p>
            <p><img src=\"{{skin url=\"images/cookies/ie7-1.gif\"}}\" alt=\"\" /></p>
        </li>
        <li>
            <p>Click the <strong>Privacy</strong> tab</p>
            <p><img src=\"{{skin url=\"images/cookies/ie7-2.gif\"}}\" alt=\"\" /></p>
        </li>
        <li>
            <p>Click the <strong>Advanced</strong> button</p>
            <p><img src=\"{{skin url=\"images/cookies/ie7-3.gif\"}}\" alt=\"\" /></p>
        </li>
        <li>
            <p>Put a check mark in the box for <strong>Override Automatic Cookie Handling</strong>,
            put another check mark in the <strong>Always accept session cookies </strong>box</p>
            <p><img src=\"{{skin url=\"images/cookies/ie7-4.gif\"}}\" alt=\"\" /></p>
        </li>
        <li>
            <p>Click <strong>OK</strong></p>
            <p><img src=\"{{skin url=\"images/cookies/ie7-5.gif\"}}\" alt=\"\" /></p>
        </li>
        <li>
            <p>Click <strong>OK</strong></p>
            <p><img src=\"{{skin url=\"images/cookies/ie7-6.gif\"}}\" alt=\"\" /></p>
        </li>
        <li>
            <p>Restart Internet Explore</p>
        </li>
    </ol>
    <p class=\"a-top\"><a href=\"#top\">Back to Top</a></p>
    <h3><a name=\"ie6\"></a>Internet Explorer 6.x</h3>
    <ol>
        <li>
            <p>Select <strong>Internet Options</strong> from the Tools menu</p>
            <p><img src=\"{{skin url=\"images/cookies/ie6-1.gif\"}}\" alt=\"\" /></p>
        </li>
        <li>
            <p>Click on the <strong>Privacy</strong> tab</p>
        </li>
        <li>
            <p>Click the <strong>Default</strong> button (or manually slide the bar down to <strong>Medium</strong>)
            under <strong>Settings</strong>. Click <strong>OK</strong></p>
            <p><img src=\"{{skin url=\"images/cookies/ie6-2.gif\"}}\" alt=\"\" /></p>
        </li>
    </ol>
    <p class=\"a-top\"><a href=\"#top\">Back to Top</a></p>
    <h3><a name=\"firefox\"></a>Mozilla/Firefox</h3>
    <ol>
        <li>
            <p>Click on the <strong>Tools</strong>-menu in Mozilla</p>
        </li>
        <li>
            <p>Click on the <strong>Options...</strong> item in the menu - a new window open</p>
        </li>
        <li>
            <p>Click on the <strong>Privacy</strong> selection in the left part of the window. (See image below)</p>
            <p><img src=\"{{skin url=\"images/cookies/firefox.png\"}}\" alt=\"\" /></p>
        </li>
        <li>
            <p>Expand the <strong>Cookies</strong> section</p>
        </li>
        <li>
            <p>Check the <strong>Enable cookies</strong> and <strong>Accept cookies normally</strong> checkboxes</p>
        </li>
        <li>
            <p>Save changes by clicking <strong>Ok</strong>.</p>
        </li>
    </ol>
    <p class=\"a-top\"><a href=\"#top\">Back to Top</a></p>
    <h3><a name=\"opera\"></a>Opera 7.x</h3>
    <ol>
        <li>
            <p>Click on the <strong>Tools</strong> menu in Opera</p>
        </li>
        <li>
            <p>Click on the <strong>Preferences...</strong> item in the menu - a new window open</p>
        </li>
        <li>
            <p>Click on the <strong>Privacy</strong> selection near the bottom left of the window. (See image below)</p>
            <p><img src=\"{{skin url=\"images/cookies/opera.png\"}}\" alt=\"\" /></p>
        </li>
        <li>
            <p>The <strong>Enable cookies</strong> checkbox must be checked, and <strong>Accept all cookies</strong>
            should be selected in the &quot;<strong>Normal cookies</strong>&quot; drop-down</p>
        </li>
        <li>
            <p>Save changes by clicking <strong>Ok</strong></p>
        </li>
    </ol>
    <p class=\"a-top\"><a href=\"#top\">Back to Top</a></p>
</div>
",
        'is_active'     => 1,
        'stores'        => array(0)
    )
);

/**
 * Insert default blocks
 */
foreach ($cmsBlocks as $data) {
    Mage::getModel('cms/block')->setData($data)->save();
}

/**
 * Insert default and system pages
 */
foreach ($cmsPages as $data) {
    Mage::getModel('cms/page')->setData($data)->save();
}

$content = '
<div class="links">
    <div class="block-title">
        <strong><span>Company</span></strong>
    </div>
    <ul>
        <li><a href="{{store url=""}}about-magento-demo-store/">About Us</a></li>
        <li><a href="{{store url=""}}contacts/">Contact Us</a></li>
        <li><a href="{{store url=""}}customer-service/">Customer Service</a></li>
        <li><a href="{{store url=""}}privacy-policy-cookie-restriction-mode/">Privacy Policy</a></li>
    </ul>
</div>';

$cmsBlock = array(
    'title'         => 'Footer Links Company',
    'identifier'    => 'footer_links_company',
    'content'       => $content,
    'is_active'     => 1,
    'stores'        => 0
);

Mage::getModel('cms/block')->setData($cmsBlock)->save();
