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
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Cms\Helper;

use Mage;
use Mage_Cms_Helper_Page as Subject;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Cms\CmsTrait;
use PHPUnit\Framework\TestCase;

class PageTest extends TestCase
{
    use CmsTrait;

    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('cms/page');
    }

    /**
     * @dataProvider provideGetUsedInStoreConfigPaths
     * @group Mage_Cms
     * @group Mage_Cms_Helper
     */
    public function testGetUsedInStoreConfigPaths(array $expectedResult, ?array $path): void
    {
        $this->assertSame($expectedResult, Subject::getUsedInStoreConfigPaths($path));
    }
}
