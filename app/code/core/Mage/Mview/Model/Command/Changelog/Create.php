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
 * Mage_Mview_Model_Command_Create
 *
 * @category    Mage
 * @package     Mage_Mview
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Mview_Model_Command_Changelog_Create extends Mage_Mview_Model_Command_Abstract
{

    /**
     * @var Magento_Db_Object_Table
     */
    protected $_mview       = null;

    /**
     * @var Magento_Db_Object_Table
     */
    protected $_changelog   = null;

    /**
     * @var string
     */
    protected $_ruleColumn  = null;

    /**
     * Constructor
     *
     * @param $arguments array
     */
    public function __construct($arguments)
    {
        $this->_mview       = $arguments['mview'];
        $this->_changelog   = $arguments['changelog'];
        $this->_ruleColumn  = $arguments['rule_column'];
        parent::__construct($arguments);
    }

    /**
     * Create materialized view
     *
     * @return Mage_Mview_Model_Command_Create|mixed
     * @throws Exception
     * @throws Exception
     */
    public function execute()
    {
        if (!$this->_mview->isExists() || $this->_changelog->isExists()) {
            throw new Exception('Mview does not exists or changelog with same name already exists!!!');
        }
        $describe = $this->_mview->describe();
        $column = $describe[$this->_ruleColumn];

        $table = $this->_connection->newTable()
            ->addColumn($this->_ruleColumn, $column['DATA_TYPE'],
                implode(',', array($column['LENGTH'], $column['SCALE'], $column['PRECISION'])),
                array(
                    'unsigned'  => $column['UNSIGNED'],
                    'nullable'  => false,
                    'primary'   => true,
                ), 'Id')
            ->setComment(sprintf('Changelog for table `%s`', $this->_mview->getName()));
        $this->_changelog->create($table);
        return $this;
    }
}

