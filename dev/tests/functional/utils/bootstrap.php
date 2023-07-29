<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Tests
 * @package    Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
umask(0);

$mtfRoot = dirname(__DIR__);
$mtfRoot = str_replace('\\', '/', $mtfRoot);
define('MTF_BP', $mtfRoot);
define('MTF_TESTS_PATH', MTF_BP . '/tests/app/');

$appRoot = dirname(dirname(dirname(dirname(__DIR__))));
require __DIR__ . '/../vendor/autoload.php';

$objectManager = \Magento\Mtf\ObjectManagerFactory::getObjectManager();
\Magento\Mtf\ObjectManagerFactory::configure($objectManager);
