<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @group Mage_Directory
 * @group Mage_Directory_Block
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Directory\Block;

use Mage;
use Mage_Directory_Block_Data as Subject;
use Mage_Directory_Model_Resource_Country_Collection;
use Mage_Directory_Model_Resource_Region_Collection;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = new Subject();
    }

    
    public function testGetCountryCollection(): void
    {
        $this->assertInstanceOf(Mage_Directory_Model_Resource_Country_Collection::class, $this->subject->getCountryCollection());
    }

    
    public function testGetRegionCollection(): void
    {
        $this->assertInstanceOf(Mage_Directory_Model_Resource_Region_Collection::class, $this->subject->getRegionCollection());
    }
}
