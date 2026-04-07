<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Downloadable\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable;

use Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_Links as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Downloadable\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable\LinksTrait;

final class LinksTest extends OpenMageTest
{
    use LinksTrait;

    # private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        # self::$subject = new Subject();
        self::markTestSkipped('');
    }
}
