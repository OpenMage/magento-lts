<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sitemap\Model\Resource\Sitemap;

use Mage;
use Mage_Sitemap_Model_Resource_Sitemap_Collection as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class CollectionTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('sitemap/resource_sitemap_collection');
    }
}
