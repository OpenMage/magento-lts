<?php
require_once("Varien/Directory/Collection.php");
require_once("Varien/File/Object.php");

class Varien_Directory_Factory{
	/**
     * return or Varien_Directory_Collection or Varien_File_Object object
     *
     * @param   array $path - path to direcctory
     * @param   bool $is_recursion - use or not recursion
     * @param   int $recurse_level - recurse level
     * @return  IFactor - Varien_Directory_Collection or Varien_File_Object object
     */	
	static public function getFactory($path,$is_recursion = true,$recurse_level=0)
	{
		if(is_dir($path)){
			$obj = new Varien_Directory_Collection($path,$is_recursion,$recurse_level+1);
			return $obj;
		} else {
			return new Varien_File_Object($path);
		}
	}
	
}
?>