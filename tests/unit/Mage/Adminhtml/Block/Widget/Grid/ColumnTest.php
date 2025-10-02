<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\Widget\Grid;

use Mage_Adminhtml_Block_Widget_Grid_Column as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class ColumnTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        self::$subject = new Subject();
    }

    /**
     * @group Block
     */
    public function testGetType(): void
    {
        self::assertSame('', self::$subject->getType());

        self::$subject->setType('text');
        self::assertSame('text', self::$subject->getType());
    }
}
