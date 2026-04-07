<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Bundle\Block\Adminhtml\Catalog\Product\Composite\Fieldset\Options\Type;

use Mage_Bundle_Block_Adminhtml_Catalog_Product_Composite_Fieldset_Options_Type_Checkbox as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Bundle\Block\Adminhtml\Catalog\Product\Composite\Fieldset\Options\Type\CheckboxTrait;

final class CheckboxTest extends OpenMageTest
{
    use CheckboxTrait;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::markTestSkipped('');
    }
}
