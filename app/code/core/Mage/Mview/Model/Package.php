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
 * @category    Mage
 * @package     Mage_Mview
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Mage_Mview_Model_Package
 *
 * @category    Mage
 * @package     Mage_Mview
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Mview_Model_Package
{
    /**
     * @var Mage_Mview_Model_Factory
     */
    protected $_factory    = null;

    /**
     * @var Mage_Mview_Model_Command_Factory
     */
    protected $_commandFactory  = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_factory         = Mage::getModel('mview/factory');
        $this->_commandFactory  = $this->_factory->getModel('mview/command_factory', array(
            'factory' => $this->_factory
        ));
    }

    /**
     * Create new materialized view
     *
     * @param $mviewName
     * @param Zend_Db_Select $select
     * @return Mage_Mview_Model_Mview
     */
    public function create($mviewName, Zend_Db_Select $select)
    {
        $mview = $this->_factory->getModel('mview/mview', array('factory' => $this->_factory))
            ->setMviewName($mviewName);
        $this->_commandFactory->getCommandCreate(
            $select, $mview->getMviewName(), $mview->getViewName())
            ->execute();
        return $mview->setRefreshedAt(now())
            ->setStatus(Mage_Mview_Model_Mview::MVIEW_STATUS_VALID)
            ->save();
    }

    /**
     * Drop materialized view
     *
     * @param $mviewName
     * @return Mage_Mview_Model_Package
     */
    public function drop($mviewName)
    {
        $this->_factory->getModel('mview/mview', array('factory' => $this->_factory))
            ->load($mviewName, 'mview_name')
            ->drop();
        return $this;
    }

    /**
     * Returns materialized view by name
     *
     * @param $mviewName
     * @return Mage_Core_Model_Abstract
     */
    public function get($mviewName)
    {
        return $this->_factory->getModel('mview/mview', array('factory' => $this->_factory))
            ->load($mviewName, 'mview_name');
    }
}
