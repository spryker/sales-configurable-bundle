<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SalesConfigurableBundle\Persistence\Propel\Mapper;

use ArrayObject;
use Generated\Shared\Transfer\SalesOrderConfiguredBundleCollectionTransfer;
use Generated\Shared\Transfer\SalesOrderConfiguredBundleItemTransfer;
use Generated\Shared\Transfer\SalesOrderConfiguredBundleTransfer;
use Orm\Zed\SalesConfigurableBundle\Persistence\SpySalesOrderConfiguredBundle;
use Orm\Zed\SalesConfigurableBundle\Persistence\SpySalesOrderConfiguredBundleItem;
use Propel\Runtime\Collection\Collection;

class SalesOrderConfiguredBundleMapper
{
    public function mapBundleEntityCollectionToBundleTransferCollection(
        Collection $bundleEntities
    ): SalesOrderConfiguredBundleCollectionTransfer {
        $bundleCollectionTransfer = new SalesOrderConfiguredBundleCollectionTransfer();

        foreach ($bundleEntities as $bundleEntity) {
            $bundleTransfer = $this->mapBundleEntityToBundleTransfer(
                $bundleEntity,
                new SalesOrderConfiguredBundleTransfer(),
            );
            $bundleCollectionTransfer->addSalesOrderConfiguredBundle($bundleTransfer);
        }

        return $bundleCollectionTransfer;
    }

    public function mapBundleTransferToBundleEntity(
        SalesOrderConfiguredBundleTransfer $bundleTransfer,
        SpySalesOrderConfiguredBundle $bundleEntity
    ): SpySalesOrderConfiguredBundle {
        $bundleEntity->fromArray($bundleTransfer->modifiedToArray());

        return $bundleEntity;
    }

    public function mapBundleEntityToBundleTransfer(
        SpySalesOrderConfiguredBundle $bundleEntity,
        SalesOrderConfiguredBundleTransfer $bundleTransfer
    ): SalesOrderConfiguredBundleTransfer {
        $bundleTransfer = $bundleTransfer->fromArray($bundleEntity->toArray(), true);

        $bundleTransfer->setSalesOrderConfiguredBundleItems(
            new ArrayObject($this->mapBundleEntityToBundleItemTransfers($bundleEntity)),
        );

        return $bundleTransfer;
    }

    public function mapBundleItemTransferToBundleItemEntity(
        SalesOrderConfiguredBundleItemTransfer $bundleItemTransfer,
        SpySalesOrderConfiguredBundleItem $bundleItemEntity
    ): SpySalesOrderConfiguredBundleItem {
        $bundleItemEntity->fromArray($bundleItemTransfer->modifiedToArray());

        $bundleItemEntity
            ->setFkSalesOrderConfiguredBundle($bundleItemTransfer->getIdSalesOrderConfiguredBundle())
            ->setFkSalesOrderItem($bundleItemTransfer->getIdSalesOrderItem());

        return $bundleItemEntity;
    }

    public function mapBundleItemEntityToBundleItemTransfer(
        SpySalesOrderConfiguredBundleItem $bundleItemEntity,
        SalesOrderConfiguredBundleItemTransfer $bundleItemTransfer
    ): SalesOrderConfiguredBundleItemTransfer {
        $bundleItemTransfer = $bundleItemTransfer->fromArray($bundleItemEntity->toArray(), true);

        $bundleItemTransfer
            ->setIdSalesOrderConfiguredBundle($bundleItemEntity->getFkSalesOrderConfiguredBundle())
            ->setIdSalesOrderItem($bundleItemEntity->getFkSalesOrderItem());

        return $bundleItemTransfer;
    }

    /**
     * @param \Orm\Zed\SalesConfigurableBundle\Persistence\SpySalesOrderConfiguredBundle $bundleEntity
     *
     * @return array<\Generated\Shared\Transfer\SalesOrderConfiguredBundleItemTransfer>
     */
    protected function mapBundleEntityToBundleItemTransfers(SpySalesOrderConfiguredBundle $bundleEntity): array
    {
        $bundleItemTransfers = [];

        foreach ($bundleEntity->getSpySalesOrderConfiguredBundleItems() as $bundleItemEntity) {
            $bundleItemTransfers[] = $this->mapBundleItemEntityToBundleItemTransfer($bundleItemEntity, new SalesOrderConfiguredBundleItemTransfer());
        }

        return $bundleItemTransfers;
    }
}
