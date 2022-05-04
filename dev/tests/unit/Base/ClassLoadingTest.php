<?php


namespace OpenMage\Tests\Unit\Base;

use PHPUnit\Framework\TestCase;

class ClassLoadingTest extends TestCase
{

    public function testClassExists()
    {
        $this->assertTrue(class_exists('Mage'));
        $this->assertTrue(class_exists('Mage_Eav_Model_Entity_Increment_Numeric'));
    }

    public function testClassDoesNotExists()
    {
        $this->assertFalse(class_exists('Mage_Non_Existent'));
    }
}
