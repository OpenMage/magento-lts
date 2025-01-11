<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Cms
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
