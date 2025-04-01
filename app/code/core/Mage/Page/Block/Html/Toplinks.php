<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Page
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Page
 *
 * @deprecated after 1.4.0.1
 */
class Mage_Page_Block_Html_Toplinks extends Mage_Core_Block_Template
{
    /**
     * Array of toplinks
     *
     * array(
     *  [$index] => array(
     *                  ['liParams']
     *                  ['aParams']
     *                  ['innerText']
     *                  ['beforeText']
     *                  ['afterText']
     *                  ['first']
     *                  ['last']
     *              )
     * )
     *
     * @var array
     */
    protected $_toplinks = [];

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('page/html/top.links.phtml');
    }

    /**
     * Add link
     *
     * @param string|array $liParams
     * @param string|array $aParams
     * @param string $innerText
     * @param string|int $position
     * @param string $beforeText
     * @param string $afterText
     * @return $this
     */
    public function addLink($liParams, $aParams, $innerText, $position = '', $beforeText = '', $afterText = '')
    {
        $params = '';
        if (!empty($liParams) && is_array($liParams)) {
            foreach ($liParams as $key => $value) {
                $params .= ' ' . $key . '="' . addslashes($value) . '"';
            }
        } elseif (is_string($liParams)) {
            $params .= ' ' . $liParams;
        }
        $toplinkInfo['liParams'] = $params;
        $params = '';
        if (!empty($aParams) && is_array($aParams)) {
            foreach ($aParams as $key => $value) {
                $params .= ' ' . $key . '="' . addslashes($value) . '"';
            }
        } elseif (is_string($aParams)) {
            $params .= ' ' . $aParams;
        }
        $toplinkInfo['aParams'] = $params;
        $toplinkInfo['innerText'] = $innerText;
        $toplinkInfo['beforeText'] = $beforeText;
        $toplinkInfo['afterText'] = $afterText;
        $this->_prepareArray($toplinkInfo, ['liParams', 'aParams', 'innerText', 'beforeText', 'afterText', 'first', 'last']);
        if (is_numeric($position)) {
            array_splice($this->_toplinks, $position, 0, [$toplinkInfo]);
        } else {
            $this->_toplinks[] = $toplinkInfo;
        }
        return $this;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if (is_array($this->_toplinks) && $this->_toplinks) {
            $this->_toplinks[array_key_first($this->_toplinks)]['first'] = true;
            $this->_toplinks[array_key_last($this->_toplinks)]['last'] = true;
        }
        $this->assign('toplinks', $this->_toplinks);
        return parent::_toHtml();
    }
}
