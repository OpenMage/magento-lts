<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Shell
 */

require_once 'abstract.php';

/**
 * Magento Admin Notification Cleaner Shell Script
 *
 * Provides CLI functionality to clean, preview, and delete admin notifications.
 * Supports filtering by date, read status, severity, and a dry-run mode with table output.
 * Severity is displayed with its textual meaning (Critical, Major, Minor, Notice).
 * IMPORTANT: For CLI arguments, use space (not =) between option and value, e.g. --before 2024-01-01
 *
 * By default, unread notifications are protected and will NOT be deleted unless
 * --include-unread is explicitly specified.
 * If --all is used without --include-unread, the script aborts with an error and reports the count of unread messages.
 *
 * @package    Mage_Shell
 */
class Mage_Shell_CleanAdminNotifications extends Mage_Shell_Abstract
{
    /**
     * Maximum number of notifications to display in dry-run/table output.
     */
    private const DRY_RUN_LIMIT = 50;

    /**
     * Table width for dry-run output formatting.
     */
    private const TABLE_WIDTH = 140;

    /**
     * Maximum length for title/description truncation in dry-run output.
     */
    private const COLUMN_TRUNCATE_LENGTH = 40;

    /**
     * Severity mapping based on OpenMage/Magento LTS conventions.
     *
     * @var array<int, string>
     */
    protected $severityMap = [
        0 => 'Notice',
        1 => 'Critical',
        2 => 'Major',
        3 => 'Minor',
    ];

    /**
     * Run the shell logic for cleaning admin notifications.
     *
     * Supports:
     *  --before          Delete notifications before a specific date (format: YYYY-MM-DD or YYYY-MM-DD HH:MM:SS)
     *  --only-read       Delete only notifications marked as read
     *  --include-unread  Allows deletion of unread notifications (use with caution!)
     *  --severity-0      Only notifications with severity Notice
     *  --severity-1      Only notifications with severity Critical
     *  --severity-2      Only notifications with severity Major
     *  --severity-3      Only notifications with severity Minor
     *  --all             Delete all notifications (requires --include-unread for unread notifications)
     *  --dry-run         Show notifications that would be deleted (table output)
     *  help              Display usage information
     *
     * Example: php shell/cleanAdminNotifications.php --before 2024-01-01 --severity-0 --dry-run
     */
    public function run(): void
    {
        // Read CLI arguments, support fallback for direct _args usage
        $before = $this->getArg('before');
        if (!$before && isset($this->_args['before'])) {
            $before = $this->_args['before'];
        }
        $onlyRead = $this->getArg('only-read') ?: ($this->_args['only-read'] ?? null);
        $includeUnread = $this->getArg('include-unread') ?: ($this->_args['include-unread'] ?? null);
        $all = $this->getArg('all') ?: ($this->_args['all'] ?? null);
        $dryRun = $this->getArg('dry-run') ?: ($this->_args['dry-run'] ?? null);

        // Parse severity options
        $severities = [];
        foreach ([0, 1, 2, 3] as $sev) {
            if ($this->getArg('severity-' . $sev) || isset($this->_args['severity-' . $sev])) {
                $severities[] = $sev;
            }
        }

        $conn = Mage::getSingleton('core/resource')->getConnection('core_write');
        $table = Mage::getSingleton('core/resource')->getTableName('adminnotification/inbox');

        // PROTECTION: By default, protect unread notifications unless --include-unread is present
        $protectUnread = !$includeUnread;

        // If --all is used and --include-unread is not present, abort with error and count
        if ($all && $protectUnread) {
            // Count unread notifications
            $countUnread = $conn->fetchOne("SELECT COUNT(*) FROM $table WHERE is_read = 0");
            echo "ERROR: Cannot delete ALL notifications unless --include-unread is specified!\n";
            if ($countUnread > 0) {
                echo "There are $countUnread unread notifications present in the database. Unread notifications are protected by default.\n";
            } else {
                echo "Unread notifications are protected by default.\n";
            }
            echo "To delete all notifications, including unread, use:\n";
            echo "  php shell/cleanAdminNotifications.php --all --include-unread\n";
            return;
        }

        if ($dryRun) {
            // Build SELECT for dry-run
            $select = $conn->select()
                ->from($table, [
                    'notification_id',
                    'title',
                    'description',
                    'date_added',
                    'is_read',
                    'severity',
                ]);
            if (!$all) {
                if ($before) {
                    $select->where('date_added < ?', $before);
                }
                // Apply unread protection unless include-unread is present
                if ($protectUnread || $onlyRead) {
                    $select->where('is_read = ?', 1);
                }
                if (!empty($severities)) {
                    $select->where('severity IN (?)', $severities);
                }
            }
            $select->limit(self::DRY_RUN_LIMIT);

            $rows = $conn->fetchAll($select);

            if (count($rows)) {
                // Print table header
                printf("%-5s | %-40s | %-40s | %-20s | %-6s | %-8s\n", 'ID', 'Title', 'Description', 'Date Added', 'Read', 'Severity');
                printf("%s\n", str_repeat('-', self::TABLE_WIDTH));
                foreach ($rows as $row) {
                    // Truncate title and description for display
                    $desc = isset($row['description']) ? substr((string) $row['description'], 0, self::COLUMN_TRUNCATE_LENGTH) : '';
                    $severityLabel = $this->severityMap[(int) $row['severity']] ?? (string) $row['severity'];
                    printf(
                        "%-5d | %-40s | %-40s | %-20s | %-6s | %-8s\n",
                        (int) $row['notification_id'],
                        substr((string) $row['title'], 0, self::COLUMN_TRUNCATE_LENGTH),
                        $desc,
                        (string) $row['date_added'],
                        ((int) $row['is_read'] === 1 ? 'Yes' : 'No'),
                        $severityLabel,
                    );
                }
                if (count($rows) == self::DRY_RUN_LIMIT) {
                    echo '(Showing first ' . self::DRY_RUN_LIMIT . " results)\n";
                }
                // Show total count of matching notifications
                $selectCount = $conn->select()->from($table, 'COUNT(*)');
                if (!$all) {
                    if ($before) {
                        $selectCount->where('date_added < ?', $before);
                    }
                    if ($protectUnread || $onlyRead) {
                        $selectCount->where('is_read = ?', 1);
                    }
                    if (!empty($severities)) {
                        $selectCount->where('severity IN (?)', $severities);
                    }
                }
                $countTotal = $conn->fetchOne($selectCount);
                echo "Total notifications that would be deleted: $countTotal\n";
            } else {
                echo "No notifications would be deleted.\n";
            }
        } else {
            // Prepare WHERE for delete
            $where = [];
            if (!$all) {
                if ($before) {
                    $where[] = $conn->quoteInto('date_added < ?', $before);
                }
                // Apply unread protection unless include-unread is present
                if ($protectUnread || $onlyRead) {
                    $where[] = $conn->quoteInto('is_read = ?', 1);
                }
                if (!empty($severities)) {
                    $where[] = $conn->quoteInto('severity IN (?)', $severities);
                }
            }

            // Refactored for clarity, as per suggestion:
            if ($all) {
                $whereClause = '';
            } elseif (count($where)) {
                $whereClause = implode(' AND ', $where);
            } else {
                $whereClause = '';
            }

            if ($all) {
                $count = $conn->delete($table); // Delete everything (only possible if --include-unread)
            } elseif ($whereClause !== '') {
                $count = $conn->delete($table, $whereClause); // Delete with filter
            } else {
                echo "No filters specified, and --all not set. Nothing deleted.\n";
                return;
            }
            echo "Deleted $count notifications.\n";
        }
    }

    /**
     * Display usage/help for the CLI script.
     */
    public function usageHelp(): string
    {
        return
            "Usage:  php -f cleanAdminNotifications.php [options]\n" .
            "  --before YYYY-MM-DD    Delete notifications before this date (space, not =)\n" .
            "  --only-read            Delete only notifications marked as read (default: ON unless --include-unread is used)\n" .
            "  --include-unread       Allows deletion of unread notifications (use with caution!)\n" .
            "  --severity-0           Only notifications with severity Notice\n" .
            "  --severity-1           Only notifications with severity Critical\n" .
            "  --severity-2           Only notifications with severity Major\n" .
            "  --severity-3           Only notifications with severity Minor\n" .
            "  --all                  Delete all notifications (requires --include-unread for unread notifications)\n" .
            "  --dry-run              Show what would be deleted, no changes\n" .
            "  help                   This help message\n" .
            "\nIMPORTANT: For options with values, use space (not =) between option and value!\n" .
            "Example: php shell/cleanAdminNotifications.php --before 2024-01-01 --severity-0 --dry-run\n" .
            "         php shell/cleanAdminNotifications.php --all --include-unread\n";
    }
}

// Instantiate and run the shell script
$shell = new Mage_Shell_CleanAdminNotifications();
$shell->run();
