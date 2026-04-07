<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Eav\Model\Convert\Parser;

// use Mage;
// use Mage_Eav_Model_Convert_Parser_Abstract as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Eav\Model\Convert\Parser\AbstractTrait;

final class AbstractTest extends OpenMageTest
{
    use AbstractTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('eav/convert_parser_abstract');
        self::markTestSkipped('');
    }
}
