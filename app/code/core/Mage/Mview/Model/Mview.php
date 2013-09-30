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
 * Mage_Mview_Model_Mview
 *
 * @category    Mage
 * @package     Mage_Mview
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Mview_Model_Mview extends Mage_Mview_Model_Abstract
{
    const MVIEW_STATUS_VALID            = 'VALID';
    const MVIEW_STATUS_INVALID          = 'INVALID';
    const MVIEW_STATUS_REFRESH_FAILED   = 'REFRESH FAILED';
    const MVIEW_STATUS_OUT_OF_DATE      = 'OUT OF DATE';

    /**
     * @var Mage_Mview_Model_Command_Factory
     */
    protected $_commandFactory = null;

    /**
     * Model initialization
     */
    protected function _construct()
    {
        $this->_init('mview/mview');
        $this->_commandFactory = $this->_factory->getModel('mview/command_factory', array(
            'factory' => $this->_factory
        ));
    }

    /**
     * Returns view name for materialized view
     *
     * @param $mviewName
     * @return string
     */
    protected function _generateViewName($mviewName)
    {
        return 'vw_' . $mviewName;
    }

    /**
     * Returns changelog table name for materialized view
     *
     * @param $mviewName
     * @return string
     */
    protected function _generateChangelogName($mviewName)
    {
        return 'cl_' . $mviewName;
    }

    /**
     * Set materialized view name and view name
     *
     * @param $mviewName
     * @return Mage_Mview_Model_Mview
     */
    public function setMviewName($mviewName)
    {
        $this->setData('mview_name', $mviewName);
        $this->setData('view_name', $this->_generateViewName($mviewName));
        return $this;
    }

    /**
     * Initialize rule column for materialized view
     *
     * @param $ruleColumn
     * @return Mage_Mview_Model_Mview
     * @throws Exception
     */
    public function setRuleColumn($ruleColumn)
    {
        if (!$this->getId()) {
            throw new Exception(
                'Cann\'t initialize rule column for materialized view, because metadata doesn\'t exists!!');
        }
        $this->setData('rule_column', $ruleColumn)
            ->save();
        return $this;
    }

    public function getChangelog()
    {
        return $this->_factory->getModel('mview/mview_changelog')
            ->setMviewId($this->getId());
    }

    /**
     * Create changelog table
     *
     * @throws Exception
     */
    public function createChangelog()
    {
        if (!$this->getId() || $this->getChangelogName()) {
            throw new Exception(
                'Cann\'t create changelog table because materialized'
                . ' view doesn\'t exists or changelog already exists!!');
        }
        $this->setData('changelog_name', $this->_generateChangelogName($this->getMviewName()));
        $this->_commandFactory->getCommandChangelogCreate(
            $this->getMviewName(), $this->getChangelogName(), $this->getRuleColumn())
            ->execute();
        $this->save();
    }

    /**
     * Drop materialized view
     *
     * @throws Exception
     * @throws Exception
     */
    public function drop()
    {
        if (!$this->getMviewName() || !$this->getViewName()) {
            throw new Exception(
                'Cann\'t drop materialized view, because metadata doesn\'t contains enough information!!');
        }
        try {
            $this->_commandFactory->getCommandDrop($this->getMviewName(), $this->getViewName())
                ->execute();
            $this->delete();
        } catch (Exception $e) {
            $this->setStatus(self::MVIEW_STATUS_INVALID)
                ->save();
            throw $e;
        }
    }

    /**
     * Refresh materialized view
     *
     * @throws Exception
     * @throws Exception
     */
    public function refresh()
    {
        if (!$this->getMviewName() || !$this->getViewName()) {
            throw new Exception(
                'Cann\'t refresh materialized view, because metadata doesn\'t contains enough information!!');
        }
        try {
            $this->_commandFactory->getCommandRefresh($this->getMviewName(), $this->getViewName())
                ->execute();
            $this->setStatus(self::MVIEW_STATUS_VALID)
                ->setRefreshedAt(now());
        } catch (Exception $e) {
            $this->setStatus(self::MVIEW_STATUS_INVALID);
            throw $e;
        }
        $this->save();
    }

    /**
     * Refresh row of materialized view
     *
     * @param $value
     * @throws Exception
     * @throws Exception
     */
    public function refreshRow($value)
    {
        if (!$this->getMviewName() || !$this->getViewName() || !$this->getRuleColumn() || !$value) {
            throw new Exception(
                'Cann\'t refresh row of materialized view, because metadata doesn\'t contains enough information!!');
        }
        try {
            $this->_commandFactory->getCommandRefreshRow(
                $this->getMviewName(), $this->getViewName(), $this->getRuleColumn(), $value)
                ->execute();
        } catch (Exception $e) {
            $this->setStatus(self::MVIEW_STATUS_INVALID)
                ->save();
            throw $e;
        }
    }
}
