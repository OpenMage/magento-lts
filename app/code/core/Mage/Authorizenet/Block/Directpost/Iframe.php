<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Authorizenet
 */

/**
 * DirectPost iframe block
 *
 * @category   Mage
 * @package    Mage_Authorizenet
 */
class Mage_Authorizenet_Block_Directpost_Iframe extends Mage_Core_Block_Template
{
    /**
     * Request params
     * @var array
     */
    protected $_params = [];

    /**
     * Set template for iframe
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('authorizenet/directpost/iframe.phtml');
    }

    /**
     * Set output params
     *
     * @param array $params
     * @return $this
     */
    public function setParams($params)
    {
        $this->_params = $params;
        return $this;
    }

    /**
     * Get params
     *
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }
}
