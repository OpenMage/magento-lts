<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\CurrencySymbol\Model\System;

// use Mage;
// use Mage_CurrencySymbol_Model_System_Currencysymbol as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\CurrencySymbol\Model\System\CurrencysymbolTrait;

final class CurrencysymbolTest extends OpenMageTest
{
    use CurrencysymbolTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('currencysymbol/system_currencysymbol');
        self::markTestSkipped('');
    }
}
