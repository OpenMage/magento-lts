<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\CurrencySymbol\Block\Adminhtml\System;

// use Mage_CurrencySymbol_Block_Adminhtml_System_Currencysymbol as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\CurrencySymbol\Block\Adminhtml\System\CurrencysymbolTrait;

final class CurrencysymbolTest extends OpenMageTest
{
    use CurrencysymbolTrait;

    // private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = new Subject();
        self::markTestSkipped('');
    }
}
