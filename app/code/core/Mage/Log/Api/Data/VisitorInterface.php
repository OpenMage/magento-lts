<?php

declare(strict_types=1);

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Log
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

interface Mage_Log_Api_Data_VisitorInterface
{
    public const DATA_ID                = 'visitor_id';
    public const DATA_FIRST_VISIT_AT    = 'first_visit_at';
    public const DATA_LAST_URL_ID       = 'last_url_id';
    public const DATA_LAST_VISIT_AT     = 'last_visit_at';
    public const DATA_SESSION_ID        = 'session_id';
    public const DATA_STORE_ID          = 'store_id';

    public function getVisitorId(): ?int;
    public function setVisitorId(?int $value);
    public function getSessionId(): ?string;
    public function setSessionId(?string $value);
    public function getFirstVisitAt();
    public function setFirstVisitAt(?string $value);
    public function getLastVisitAt();
    public function setLastVisitAt(string $value);
    public function getLastUrlId(): int;
    public function setLastUrlId(int $value);
    public function getStoreId(): int;
    public function setStoreId(int $storeId);
}
