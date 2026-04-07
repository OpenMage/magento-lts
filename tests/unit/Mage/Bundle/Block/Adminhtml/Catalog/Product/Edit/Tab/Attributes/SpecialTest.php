<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Bundle\Block\Adminhtml\Catalog\Product\Edit\Tab\Attributes;

// use Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Attributes_Special as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Bundle\Block\Adminhtml\Catalog\Product\Edit\Tab\Attributes\SpecialTrait;

final class SpecialTest extends OpenMageTest
{
    use SpecialTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = new Subject();
        self::markTestSkipped('');
    }
}
