<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Bundle\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle;

use Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Bundle\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\OptionTrait;

final class OptionTest extends OpenMageTest
{
    use OptionTrait;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::markTestSkipped('');
    }
}
