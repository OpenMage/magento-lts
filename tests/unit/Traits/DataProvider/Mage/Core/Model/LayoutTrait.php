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
use Mage_Cms_Block_Block;

trait LayoutTrait
{
    public function provideCreateBlock(): Generator
    {
        yield 'instance of Mage_Core_Block_Abstract' => [
            Mage_Cms_Block_Block::class,
            true,
            'cms/block',
            null,
            [],
        ];
        yield 'not instance of Mage_Core_Block_Abstract' => [
            false,
            false,
            'rule/conditions',
            null,
            [],
        ];
    }
}
