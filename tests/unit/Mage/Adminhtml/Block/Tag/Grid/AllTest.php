<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\Tag\Grid;

use Mage_Adminhtml_Block_Tag_Grid_All as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Adminhtml\Block\Tag\Grid\AllTrait;

final class AllTest extends OpenMageTest
{
    use AllTrait;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::markTestSkipped('');
    }
}
