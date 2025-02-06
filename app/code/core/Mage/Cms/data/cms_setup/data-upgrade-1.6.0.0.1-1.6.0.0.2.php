<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Cms
 */

$content = '<p>This website requires cookies to provide all of its features. For more ' .
    'information on what data is contained in the cookies, please see our ' .
    '<a href="{{store direct_url="privacy-policy-cookie-restriction-mode"}}">Privacy Policy page</a>. ' .
    'To accept cookies from this site, please click the Allow button below.</p>';

$cmsBlock = [
    'title'         => 'Cookie restriction notice',
    'identifier'    => 'cookie_restriction_notice_block',
    'content'       => $content,
    'is_active'     => 1,
    'stores'        => 0,
];

Mage::getModel('cms/block')->setData($cmsBlock)->save();
