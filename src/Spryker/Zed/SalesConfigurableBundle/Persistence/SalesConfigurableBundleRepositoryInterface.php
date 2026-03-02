<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SalesConfigurableBundle\Persistence;

use Generated\Shared\Transfer\SalesOrderConfiguredBundleCollectionTransfer;
use Generated\Shared\Transfer\SalesOrderConfiguredBundleFilterTransfer;

interface SalesConfigurableBundleRepositoryInterface
{
    /**
     * @param array<int> $salesOrderItemIds
     *
     * @return array<int>
     */
    public function getSalesOrderConfiguredBundleIdsBySalesOrderItemIds(array $salesOrderItemIds): array;

    public function getSalesOrderConfiguredBundleCollectionByFilter(
        SalesOrderConfiguredBundleFilterTransfer $salesOrderConfiguredBundleFilterTransfer
    ): SalesOrderConfiguredBundleCollectionTransfer;
}
