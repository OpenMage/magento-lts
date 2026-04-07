<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Directory\Model\Currency\Import;

use Mage_Directory_Model_Currency_Import_Currencyconverterapi as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Directory\Model\Currency\Import\CurrencyconverterapiTrait;

final class CurrencyconverterapiTest extends OpenMageTest
{
    use CurrencyconverterapiTrait;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::markTestSkipped('');
    }
}
