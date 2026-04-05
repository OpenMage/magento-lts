<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Tax\Model\Resource\Report\Tax;

use Mage;
use Mage_Tax_Model_Resource_Report_Tax_Updatedat as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class UpdatedatTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('tax/resource_report_tax_updatedat');
    }
}
