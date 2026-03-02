<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SalesConfigurableBundle\Business\Transformer;

use ArrayObject;
use Generated\Shared\Transfer\ConfiguredBundleTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\ProductOptionTransfer;

class ConfigurableBundleItemTransformer implements ConfigurableBundleItemTransformerInterface
{
    public function transformConfiguredBundleOrderItems(OrderTransfer $orderTransfer): OrderTransfer
    {
        $transformedOrderItems = [];

        foreach ($orderTransfer->getItems() as $itemTransfer) {
            if (!$itemTransfer->getConfiguredBundle() || $itemTransfer->getConfiguredBundle()->getQuantity() <= 1) {
                $transformedOrderItems[] = $itemTransfer;

                continue;
            }

            $transformedOrderItems = array_merge($transformedOrderItems, $this->transformConfigurableBundleItem($itemTransfer));
        }

        return $orderTransfer->setItems(new ArrayObject($transformedOrderItems));
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return array<\Generated\Shared\Transfer\ItemTransfer>
     */
    protected function transformConfigurableBundleItem(ItemTransfer $itemTransfer): array
    {
        $configuredBundleQuantity = $itemTransfer->getConfiguredBundle()->getQuantity();
        $configurableBundleItemTransfers = [];

        for ($index = 1; $index <= $configuredBundleQuantity; $index++) {
            $configurableBundleItemTransfers[] = $this->transformItemTransfer($itemTransfer, $index);
        }

        return $configurableBundleItemTransfers;
    }

    protected function transformItemTransfer(ItemTransfer $itemTransfer, int $groupKeyIndex): ItemTransfer
    {
        $transformedItemTransfer = (new ItemTransfer())
            ->fromArray($itemTransfer->toArray(), true)
            ->setQuantity($itemTransfer->getConfiguredBundleItem()->getQuantityPerSlot())
            ->setConfiguredBundle($this->transformConfiguredBundleTransfer($itemTransfer, $groupKeyIndex));

        $transformedItemTransfer = $this->transformProductOptions($transformedItemTransfer, $itemTransfer);

        return $transformedItemTransfer;
    }

    protected function transformConfiguredBundleTransfer(ItemTransfer $itemTransfer, int $groupKeyIndex): ConfiguredBundleTransfer
    {
        return (new ConfiguredBundleTransfer())
            ->fromArray($itemTransfer->getConfiguredBundle()->toArray(), true)
            ->setQuantity(1)
            ->setGroupKey($this->generateTransformedGroupKey($itemTransfer, $groupKeyIndex));
    }

    protected function generateTransformedGroupKey(ItemTransfer $itemTransfer, int $groupKeyIndex): string
    {
        return sprintf(
            '%s-%s',
            $itemTransfer->getConfiguredBundle()->getGroupKey(),
            $groupKeyIndex,
        );
    }

    protected function transformProductOptions(ItemTransfer $transformedItemTransfer, ItemTransfer $itemTransfer): ItemTransfer
    {
        $transformedProductOptions = new ArrayObject();
        foreach ($itemTransfer->getProductOptions() as $productOptionTransfer) {
            $transformedProductOptions->append(
                $this->copyProductOptionTransfer($productOptionTransfer, $itemTransfer->getConfiguredBundleItem()->getQuantityPerSlot()),
            );
        }

        $transformedItemTransfer->setProductOptions($transformedProductOptions);

        return $transformedItemTransfer;
    }

    protected function copyProductOptionTransfer(ProductOptionTransfer $productOptionTransfer, int $itemQuantity): ProductOptionTransfer
    {
        $transformedProductOptionTransfer = new ProductOptionTransfer();
        $transformedProductOptionTransfer->fromArray($productOptionTransfer->toArray(), true);

        $transformedProductOptionTransfer
            ->setQuantity($itemQuantity)
            ->setIdProductOptionValue(null);

        return $transformedProductOptionTransfer;
    }
}
