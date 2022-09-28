<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Varien
 * @package     Varien_Data
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Form hidden element
 *
 * @category   Varien
 * @package    Varien_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Varien_Data_Form_Element_Hidden extends Varien_Data_Form_Element_Abstract
{
    /**
     * Varien_Data_Form_Element_Hidden constructor.
     * @param array $attributes
     */
    public function __construct($attributes=array())
    {
        parent::__construct($attributes);
        $this->setType('hidden');
        $this->setExtType('hiddenfield');
    }

    /**
     * @return mixed|string
     */
    public function getDefaultHtml()
    {
        $html = $this->getData('default_html');
        if (is_null($html)) {
            $html = $this->getElementHtml();
        }
        return $html;
    }
}
