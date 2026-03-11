<?php

#[Mage_Core_Model_OpenMageDi]
class OpenMage_DiTest_Model_Runner
{
    private readonly OpenMage_DiTest_Model_Greeter $greeter;

    public function __construct(OpenMage_DiTest_Model_Greeter $greeter)
    {
        $this->greeter = $greeter;
    }

    public function execute(): string
    {
        return $this->greeter->greet();
    }
}
