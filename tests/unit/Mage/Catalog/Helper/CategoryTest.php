<?php

/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @group Mage_Catalog
 * @group Mage_Catalog_Helper
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Catalog\Helper;

use Mage;
use Mage_Catalog_Helper_Category as Subject;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('catalog/category');
    }


    public function testCanUseCanonicalTag(): void
    {
        $this->assertIsBool($this->subject->canUseCanonicalTag());
    }
}
