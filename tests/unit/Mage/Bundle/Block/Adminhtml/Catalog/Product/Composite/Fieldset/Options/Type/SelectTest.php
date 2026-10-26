<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Bundle\Block\Adminhtml\Catalog\Product\Composite\Fieldset\Options\Type;

// use Mage_Bundle_Block_Adminhtml_Catalog_Product_Composite_Fieldset_Options_Type_Select as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Bundle\Block\Adminhtml\Catalog\Product\Composite\Fieldset\Options\Type\SelectTrait;

final class SelectTest extends OpenMageTest
{
    use SelectTrait;

    // private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = new Subject();
        self::markTestSkipped('');
    }
}
