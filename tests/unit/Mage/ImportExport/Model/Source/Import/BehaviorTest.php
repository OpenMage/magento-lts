<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\ImportExport\Model\Source\Import;

use Mage;
use Mage_ImportExport_Model_Source_Import_Behavior as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class BehaviorTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('importexport/source_import_behavior');
    }
}
