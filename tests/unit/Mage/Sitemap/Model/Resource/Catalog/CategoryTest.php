<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sitemap\Model\Resource\Catalog;

// use Mage;
// use Mage_Sitemap_Model_Resource_Catalog_Category as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Sitemap\Model\Resource\Catalog\CategoryTrait;

final class CategoryTest extends OpenMageTest
{
    use CategoryTrait;

    // private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('sitemap/resource_catalog_category');
        self::markTestSkipped('');
    }
}
