<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

defined('MTF_BOOT_FILE') || define('MTF_BOOT_FILE', __FILE__);
defined('MTF_BP') || define('MTF_BP', str_replace('\\', '/', (__DIR__)));
defined('MTF_TESTS_PATH') || define('MTF_TESTS_PATH', MTF_BP . '/tests/app/');
defined('MTF_STATES_PATH') || define('MTF_STATES_PATH', MTF_BP . '/lib/Magento/Mtf/App/State/');

//require_once __DIR__ . '/../../../app/bootstrap.php';
restore_error_handler();
require_once __DIR__ . '/vendor/autoload.php';
