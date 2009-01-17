<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml grid item renderer datetime
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Datetime extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	/**
	 * Date format string
	 */
	protected static $_format = null;

	/**
	 * Retrieve datetime format
	 *
	 * @return unknown
	 */
	protected function _getFormat()
	{
	    $format = $this->getColumn()->getFormat();
	    if (!$format) {
            if (is_null(self::$_format)) {
                try {
                    self::$_format = Mage::app()->getLocale()->getDateTimeFormat(
                        Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM
                    );
                }
                catch (Exception $e) {

                }
			}
			$format = self::$_format;
	    }
	    return $format;
	}

    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        if ($data = $row->getData($this->getColumn()->getIndex())) {
			$format = $this->_getFormat();
			try {
                $data = Mage::app()->getLocale()->date($data, 'yyyy-MM-dd HH:mm:ss')->toString($format);
            }
            catch (Exception $e)
            {
                $data = Mage::app()->getLocale()->date($data, 'yyyy-MM-dd HH:mm:ss')->toString($format);
            }
            return $data;

        }
        return $this->getColumn()->getDefault();
    }
}