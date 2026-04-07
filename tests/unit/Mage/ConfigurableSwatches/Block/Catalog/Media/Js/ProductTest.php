<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\ConfigurableSwatches\Block\Catalog\Media\Js;

use Mage_ConfigurableSwatches_Block_Catalog_Media_Js_Product as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\ConfigurableSwatches\Block\Catalog\Media\Js\ProductTrait;

final class ProductTest extends OpenMageTest
{
    use ProductTrait;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::markTestSkipped('');
    }
}
