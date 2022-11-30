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
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method string getContentType()
 * @method $this setContentType(string $value)
 * @method string getTitle()
 * @method string getDescription()
 * @method string getKeywords()
 * @method string getRobots()
 */
class Mage_Core_Block_Text_Tag_Meta extends Mage_Core_Block_Text
{
    /**
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->getContentType()) {
            $this->setContentType('text/html; charset=utf-8');
        }
        $this->addText('<meta http-equiv="Content-Type" content="' . $this->getContentType() . '"/>' . "\n");
        $this->addText('<title>' . $this->getTitle() . '</title>' . "\n");
        $this->addText('<meta name="title" content="' . $this->getTitle() . '"/>' . "\n");
        $this->addText('<meta name="description" content="' . $this->getDescription() . '"/>' . "\n");
        $this->addText('<meta name="keywords" content="' . $this->getKeywords() . '"/>' . "\n");
        $this->addText('<meta name="robots" content="' . $this->getRobots() . '"/>' . "\n");

        return parent::_toHtml();
    }
}
