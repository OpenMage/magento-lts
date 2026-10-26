<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Product options text type block
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Block_Product_View_Options_Type_File extends Mage_Catalog_Block_Product_View_Options_Abstract
{
    /**
     * Returns info of file
     *
     * @return Varien_Object
     */
    public function getFileInfo()
    {
        $info = $this->getProduct()->getPreconfiguredValues()->getData('options/' . $this->getOption()->getId());
        if (empty($info)) {
            $info = new Varien_Object();
        } elseif (is_array($info)) {
            $info = new Varien_Object($info);
        }

        return $info;
    }
}
