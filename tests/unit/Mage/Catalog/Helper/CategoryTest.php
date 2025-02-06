<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
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

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Helper
     */
    public function testCanUseCanonicalTag(): void
    {
        $this->assertIsBool($this->subject->canUseCanonicalTag());
    }
}
