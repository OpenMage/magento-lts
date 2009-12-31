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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Page
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Page layouts source
 *
 * @category   Mage
 * @package    Mage_Page
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Page_Model_Source_Layout
{

    /**
     * Page layout options
     *
     * @var array
     */
    protected $_options = null;

    /**
     * Retrieve page layout options
     *
     * @return array
     */
    public function getOptions()
    {
        if ($this->_options === null) {
            $this->_options = array();
            foreach (Mage::getSingleton('page/config')->getPageLayouts() as $layout) {
                $this->_options[$layout->getCode()] = $layout->getLabel();
            }
        }

        return $this->_options;
    }

    /**
     * Retrieve page layout options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();

        foreach ($this->getOptions() as $value => $label) {
            $options[] = array(
                'label' => $label,
                'value'  => $value
            );
        }

        return $options;
    }
}
