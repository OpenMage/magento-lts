<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Fixture\Adminhtml\GridExport;

use Mage;
use Override;
use Varien_Data_Collection_Db;
use Varien_Object;

/**
 * In-memory rows with the real DB collection's cloning, paging and select API.
 * @extends Varien_Data_Collection_Db<Varien_Object>
 */
class ExportCollection extends Varien_Data_Collection_Db
{
    public ExportTrace $trace;

    /** @param list<array<string, mixed>> $rows */
    public function __construct(private readonly array $rows)
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('core_read'));
        $this->trace = new ExportTrace();
    }

    #[Override]
    public function load($printQuery = false, $logQuery = false)
    {
        if (!$this->isLoaded()) {
            $limit = (int) $this->getPageSize();
            $this->trace->pages[] = [$this->getCurPage(), $limit];
            $rows = $limit > 0 ? array_slice($this->rows, ($this->getCurPage() - 1) * $limit, $limit) : $this->rows;
            foreach ($rows as $row) {
                $this->addItem(new Varien_Object($row));
            }

            $this->_setIsLoaded();
        }

        return $this;
    }

    #[Override]
    public function getSize()
    {
        return count($this->rows);
    }
}
