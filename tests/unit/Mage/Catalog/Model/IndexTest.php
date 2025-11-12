<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Catalog\Model;

use Mage;
use Mage_Catalog_Model_Index as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class IndexTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('catalog/index');
    }

    /**
     * @group Model
     */
    public function testRebuild(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->rebuild());
    }
}
