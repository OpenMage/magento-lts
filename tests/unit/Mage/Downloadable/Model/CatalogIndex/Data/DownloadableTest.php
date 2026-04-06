<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Downloadable\Model\CatalogIndex\Data;

use Mage;
use Mage_Downloadable_Model_CatalogIndex_Data_Downloadable as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Downloadable\Model\CatalogIndex\Data\DownloadableTrait;

final class DownloadableTest extends OpenMageTest
{
    use DownloadableTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('downloadable/catalogindex_data_downloadable');
        self::markTestSkipped('');
    }
}
