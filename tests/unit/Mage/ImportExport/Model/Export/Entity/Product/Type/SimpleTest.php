<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\ImportExport\Model\Export\Entity\Product\Type;

// use Mage;
// use Mage_ImportExport_Model_Export_Entity_Product_Type_Simple as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\ImportExport\Model\Export\Entity\Product\Type\SimpleTrait;

final class SimpleTest extends OpenMageTest
{
    use SimpleTrait;

    // private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('importexport/export_entity_product_type_simple');
        self::markTestSkipped('');
    }
}
