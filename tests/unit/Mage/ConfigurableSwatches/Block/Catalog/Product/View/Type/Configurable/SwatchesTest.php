<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\ConfigurableSwatches\Block\Catalog\Product\View\Type\Configurable;

use Mage_ConfigurableSwatches_Block_Catalog_Product_View_Type_Configurable_Swatches as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class SwatchesTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = new Subject();
    }
}
