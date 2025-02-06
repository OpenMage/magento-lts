<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
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
