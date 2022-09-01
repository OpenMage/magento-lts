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
 * @category    Mage
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Base html block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @method string getTagContents()
 * @method $this setTagContents(string $value)
 * @method getTagName()
 * @method array getTagParams()
 * @method $this setTagParams(array $value)
 */
class Mage_Core_Block_Text_Tag extends Mage_Core_Block_Text
{

    protected function _construct()
    {
        parent::_construct();
        $this->setTagParams([]);
    }

    /**
     * @param string|array $param
     * @param null $value
     * @return $this
     */
    public function setTagParam($param, $value = null)
    {
        if (is_array($param) && is_null($value)) {
            foreach ($param as $k => $v) {
                $this->setTagParam($k, $v);
            }
        } else {
            $params = $this->getTagParams();
            $params[$param] = $value;
            $this->setTagParams($params);
        }
        return $this;
    }

    /**
     * @param string $text
     * @return $this
     */
    public function setContents($text)
    {
        $this->setTagContents($text);
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function _toHtml()
    {
        $this->setText('<'.$this->getTagName().' ');
        if ($this->getTagParams()) {
            foreach ($this->getTagParams() as $k => $v) {
                $this->addText($k.'="'.$v.'" ');
            }
        }

        $this->addText('>'.$this->getTagContents().'</'.$this->getTagName().'>'."\r\n");
        return parent::_toHtml();
    }
}
