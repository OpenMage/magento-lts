<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_AdminNotification
 */

/**
 * AdminNotification Inbox model
 *
 * @package    Mage_AdminNotification
 *
 * @method Mage_AdminNotification_Model_Resource_Inbox _getResource()
 * @method Mage_AdminNotification_Model_Resource_Inbox getResource()
 * @method Mage_AdminNotification_Model_Resource_Inbox_Collection getCollection()
 * @method string getDateAdded()
 * @method $this setDateAdded(string $value)
 * @method string getDescription()
 * @method $this setDescription(string $value)
 * @method int getIsRead()
 * @method $this setIsRead(int $value)
 * @method int getIsRemove()
 * @method $this setIsRemove(int $value)
 * @method int getSeverity()
 * @method $this setSeverity(int $value)
 * @method string getTitle()
 * @method $this setTitle(string $value)
 * @method string getUrl()
 * @method $this setUrl(string $value)
 */
class Mage_AdminNotification_Model_Inbox extends Mage_Core_Model_Abstract
{
    public const SEVERITY_CRITICAL = 1;

    public const SEVERITY_MAJOR    = 2;

    public const SEVERITY_MINOR    = 3;

    public const SEVERITY_NOTICE   = 4;

    protected function _construct()
    {
        $this->_init('adminnotification/inbox');
    }

    /**
     * Retrieve Severity collection array
     *
     * @param int|null $severity
     * @return array|string|null
     */
    public function getSeverities($severity = null)
    {
        $severities = [
            self::SEVERITY_CRITICAL => Mage::helper('adminnotification')->__('critical'),
            self::SEVERITY_MAJOR    => Mage::helper('adminnotification')->__('major'),
            self::SEVERITY_MINOR    => Mage::helper('adminnotification')->__('minor'),
            self::SEVERITY_NOTICE   => Mage::helper('adminnotification')->__('notice'),
        ];

        if (!is_null($severity)) {
            return $severities[$severity] ?? null;
        }

        return $severities;
    }

    /**
     * Retrieve Latest Notice
     *
     * @return $this
     */
    public function loadLatestNotice()
    {
        $this->setData([]);
        $this->getResource()->loadLatestNotice($this);
        return $this;
    }

    /**
     * Retrieve notice statuses
     *
     * @return array
     */
    public function getNoticeStatus()
    {
        return $this->getResource()->getNoticeStatus($this);
    }

    /**
     * Parse and save new data
     *
     * @return $this
     */
    public function parse(array $data)
    {
        return $this->getResource()->parse($this, $data);
    }

    /**
     * Add new message
     *
     * @param int $severity
     * @param string $title
     * @param string|array $description
     * @param string $url
     * @param bool $isInternal
     * @return $this
     */
    public function add($severity, $title, $description, $url = '', $isInternal = true)
    {
        if (!$this->getSeverities($severity)) {
            Mage::throwException(Mage::helper('adminnotification')->__('Wrong message type'));
        }

        if (is_array($description)) {
            $description = '<ul><li>' . implode('</li><li>', $description) . '</li></ul>';
        }

        $date = date(Varien_Db_Adapter_Pdo_Mysql::TIMESTAMP_FORMAT);
        $this->parse([[
            'severity'    => $severity,
            'date_added'  => $date,
            'title'       => $title,
            'description' => $description,
            'url'         => $url,
            'internal'    => $isInternal,
        ]]);
        return $this;
    }

    /**
     * Add critical severity message
     *
     * @param string $title
     * @param string|array $description
     * @param string $url
     * @param bool $isInternal
     * @return $this
     */
    public function addCritical($title, $description, $url = '', $isInternal = true)
    {
        $this->add(self::SEVERITY_CRITICAL, $title, $description, $url, $isInternal);
        return $this;
    }

    /**
     * Add major severity message
     *
     * @param string $title
     * @param string|array $description
     * @param string $url
     * @param bool $isInternal
     * @return $this
     */
    public function addMajor($title, $description, $url = '', $isInternal = true)
    {
        $this->add(self::SEVERITY_MAJOR, $title, $description, $url, $isInternal);
        return $this;
    }

    /**
     * Add minor severity message
     *
     * @param string $title
     * @param string|array $description
     * @param string $url
     * @param bool $isInternal
     * @return $this
     */
    public function addMinor($title, $description, $url = '', $isInternal = true)
    {
        $this->add(self::SEVERITY_MINOR, $title, $description, $url, $isInternal);
        return $this;
    }

    /**
     * Add notice
     *
     * @param string $title
     * @param string|array $description
     * @param string $url
     * @param bool $isInternal
     * @return $this
     */
    public function addNotice($title, $description, $url = '', $isInternal = true)
    {
        $this->add(self::SEVERITY_NOTICE, $title, $description, $url, $isInternal);
        return $this;
    }
}
