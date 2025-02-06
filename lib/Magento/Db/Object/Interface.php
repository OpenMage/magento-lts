<?php

/**
 * @category   Magento
 * @package    Magento_Db
 */

/**
 * Magento_Db_Object_Interface
 *
 * @category   Magento
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
