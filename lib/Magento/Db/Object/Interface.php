<?php
/**
 * Magento_Db_Object_Interface
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Magento_Db
 */
/**
 * @package    Magento_Db
 */


interface Magento_Db_Object_Interface
{
    /**
     * Describe database object
     *
     * @return array
     */
    public function describe();

    /**
     * Drop database object
     */
    public function drop();

    /**
     * Check that database object is exist
     */
    public function isExists();
}
