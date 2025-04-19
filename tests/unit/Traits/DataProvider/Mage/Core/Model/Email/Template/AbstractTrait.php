<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Model\Email\Template;

use Generator;
use Mage_Core_Model_Store;
use Mage_Core_Model_Store_Group;
use Mage_Core_Model_Website;

trait AbstractTrait
{
    public function provideValidateFileExension(): Generator
    {
        yield 'css file exists' => [
            true,
            $_SERVER['TEST_ROOT'] . '/unit/fixtures/files/test.css',
            'css',
            true,
        ];
        yield 'css file exists, but empty' => [
            false,
            $_SERVER['TEST_ROOT'] . '/unit/fixtures/files/test-empty.css',
            'css',
            true,
        ];
        yield 'css file not exists' => [
            false,
            $_SERVER['TEST_ROOT'] . '/unit/fixtures/files/test.not-exist',
            'css',
            false,
        ];
    }
}
