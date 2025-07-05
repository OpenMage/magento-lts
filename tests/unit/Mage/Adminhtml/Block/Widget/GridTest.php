<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\Widget;

use Mage_Adminhtml_Block_Widget_Grid as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Adminhtml\Block\Widget\GridTrait;

class GridTest extends OpenMageTest
{
    use GridTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = new Subject();
    }

    /**
     * @dataProvider provideAddColumnDefaultData
     * @group Block
     */
    public function testAddColumnDefaultData(array $expectedResult, array $column): void
    {
        static::assertSame($expectedResult, self::$subject->addColumnDefaultData($column));
    }
}
