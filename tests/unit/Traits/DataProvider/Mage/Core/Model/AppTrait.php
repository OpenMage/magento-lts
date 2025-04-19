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

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Model;

use Generator;
use Mage_Core_Model_Store;
use Mage_Core_Model_Store_Group;
use Mage_Core_Model_Website;

trait AppTrait
{
    public function provideGetStore(): Generator
    {
        yield 'Mage_Core_Model_Store' => [
            new Mage_Core_Model_Store(),
        ];
    }

    public function provideGetWebsite(): Generator
    {
        yield 'Mage_Core_Model_Website' => [
            new Mage_Core_Model_Website(),
        ];
    }

    public function provideGetGroup(): Generator
    {
        yield 'Mage_Core_Model_Store_Group' => [
            new Mage_Core_Model_Store_Group(),
        ];
    }
}
