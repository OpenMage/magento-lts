<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\ImportExport\Model\Import\Entity;

// use Mage;
// use Mage_ImportExport_Model_Import_Entity_Abstract as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\ImportExport\Model\Import\Entity\AbstractTrait;

final class AbstractTest extends OpenMageTest
{
    use AbstractTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('importexport/import_entity_abstract');
        self::markTestSkipped('');
    }
}
