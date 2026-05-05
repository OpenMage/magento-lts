<?php

declare(strict_types=1);

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
 * @method Mage_AdminNotification_Model_Resource_Inbox            _getResource()
 * @method Mage_AdminNotification_Model_Resource_Inbox_Collection getCollection()
 * @method Mage_AdminNotification_Model_Resource_Inbox            getResource()
 * @method Mage_AdminNotification_Model_Resource_Inbox_Collection getResourceCollection()
 */
class Mage_AdminNotification_Model_Inbox extends Mage_Core_Model_Abstract
{
    public const SEVERITY_CRITICAL = 1;

    public const SEVERITY_MAJOR    = 2;

    public const SEVERITY_MINOR    = 3;

    public const SEVERITY_NOTICE   = 4;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('adminnotification/inbox');
    }

    public function getDateAdded(): string
    {
        return (string) $this->_getData('date_added');
    }

    public function getDescription(): string
    {
        return (string) $this->_getData('description');
    }

    public function getIsRead(): int
    {
        return (int) $this->_getData('is_read');
    }

    public function getIsRemove(): int
    {
        return (int) $this->_getData('is_remove');
    }

    public function getSeverity(): int
    {
        return (int) $this->_getData('severity');
    }

    public function getTitle(): string
    {
        return (string) $this->_getData('title');
    }

    public function getUrl(): string
    {
        return (string) $this->_getData('url');
    }

    public function setDateAdded(string $value): static
    {
        return $this->setData('date_added', $value);
    }

    public function setDescription(string $value): static
    {
        return $this->setData('description', $value);
    }

    public function setIsRead(int $value): static
    {
        return $this->setData('is_read', $value);
    }

    public function setIsRemove(int $value): static
    {
        return $this->setData('is_remove', $value);
    }

    public function setSeverity(int $value): static
    {
        return $this->setData('severity', $value);
    }

    public function setTitle(string $value): static
    {
        return $this->setData('title', $value);
    }

    public function setUrl(string $value): static
    {
        return $this->setData('url', $value);
    }

    /**
     * Retrieve Severity collection array
     *
     * @param  null|int          $severity
     * @return null|array|string
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
     * @throws Mage_Core_Exception
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
     * @throws Mage_Core_Exception
     */
    public function getNoticeStatus()
    {
        return $this->getResource()->getNoticeStatus($this);
    }

    /**
     * Parse and save new data
     *
     * @param  array<array<int, array<string, mixed>>, mixed> $data
     * @return void
     * @throws Mage_Core_Exception
     * @throws Zend_Db_Adapter_Exception
     */
    public function parse(array $data)
    {
        $this->getResource()->parse($this, $data);
    }

    /**
     * Add new message
     *
     * @param  int                       $severity
     * @param  string                    $title
     * @param  array|string              $description
     * @param  string                    $url
     * @param  bool                      $isInternal
     * @return $this
     * @throws Mage_Core_Exception
     * @throws Zend_Db_Adapter_Exception
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
     * @param  string                    $title
     * @param  array|string              $description
     * @param  string                    $url
     * @param  bool                      $isInternal
     * @return $this
     * @throws Mage_Core_Exception
     * @throws Zend_Db_Adapter_Exception
     */
    public function addCritical($title, $description, $url = '', $isInternal = true)
    {
        $this->add(self::SEVERITY_CRITICAL, $title, $description, $url, $isInternal);
        return $this;
    }

    /**
     * Add major severity message
     *
     * @param  string                    $title
     * @param  array|string              $description
     * @param  string                    $url
     * @param  bool                      $isInternal
     * @return $this
     * @throws Mage_Core_Exception
     * @throws Zend_Db_Adapter_Exception
     */
    public function addMajor($title, $description, $url = '', $isInternal = true)
    {
        $this->add(self::SEVERITY_MAJOR, $title, $description, $url, $isInternal);
        return $this;
    }

    /**
     * Add minor severity message
     *
     * @param  string                    $title
     * @param  array|string              $description
     * @param  string                    $url
     * @param  bool                      $isInternal
     * @return $this
     * @throws Mage_Core_Exception
     * @throws Zend_Db_Adapter_Exception
     */
    public function addMinor($title, $description, $url = '', $isInternal = true)
    {
        $this->add(self::SEVERITY_MINOR, $title, $description, $url, $isInternal);
        return $this;
    }

    /**
     * Add notice
     *
     * @param  string                    $title
     * @param  array|string              $description
     * @param  string                    $url
     * @param  bool                      $isInternal
     * @return $this
     * @throws Mage_Core_Exception
     * @throws Zend_Db_Adapter_Exception
     */
    public function addNotice($title, $description, $url = '', $isInternal = true)
    {
        $this->add(self::SEVERITY_NOTICE, $title, $description, $url, $isInternal);
        return $this;
    }
}
