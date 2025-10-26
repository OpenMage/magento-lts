<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Catalog\Helper;

use Mage;
use Mage_Catalog_Helper_Category as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class CategoryTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('catalog/category');
    }

    /**
     * @group Helper
     */
    public function testCanUseCanonicalTag(): void
    {
        self::assertIsBool(self::$subject->canUseCanonicalTag());
    }
}
