<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Varien
 * @package     Varien_Data
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Form image file element
 *
 * @category   Varien
 * @package    Varien_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @method $this setAutosubmit(bool $false)
 */
class Varien_Data_Form_Element_Imagefile extends Varien_Data_Form_Element_Abstract
{
    /**
     * Varien_Data_Form_Element_Imagefile constructor.
     * @param array $attributes
     */
    public function __construct($attributes=array())
    {
        parent::__construct($attributes);
        $this->setType('file');
        $this->setExtType('imagefile');
        $this->setAutosubmit(false);
        $this->setData('autoSubmit', false);
        //$this->setExtType('file');
    }
}
