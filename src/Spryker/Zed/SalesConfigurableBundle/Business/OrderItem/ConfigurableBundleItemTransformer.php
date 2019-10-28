<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SalesConfigurableBundle\Business\OrderItem;

use Generated\Shared\Transfer\ItemCollectionTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Spryker\Zed\SalesConfigurableBundle\Dependency\Facade\SalesConfigurableBundleToSalesFacadeInterface;
use Spryker\Zed\SalesConfigurableBundle\SalesConfigurableBundleConfig;

class ConfigurableBundleItemTransformer implements ConfigurableBundleItemTransformerInterface
{
    /**
     * @var \Spryker\Zed\SalesConfigurableBundle\Dependency\Facade\SalesConfigurableBundleToSalesFacadeInterface
     */
    protected $salesFacade;

    /**
     * @var \Spryker\Zed\SalesConfigurableBundle\SalesConfigurableBundleConfig
     */
    protected $config;

    /**
     * @param \Spryker\Zed\SalesConfigurableBundle\Dependency\Facade\SalesConfigurableBundleToSalesFacadeInterface $salesFacade
     * @param \Spryker\Zed\SalesConfigurableBundle\SalesConfigurableBundleConfig $config
     */
    public function __construct(
        SalesConfigurableBundleToSalesFacadeInterface $salesFacade,
        SalesConfigurableBundleConfig $config
    ) {
        $this->salesFacade = $salesFacade;
        $this->config = $config;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return \Generated\Shared\Transfer\ItemCollectionTransfer
     */
    public function transformConfigurableBundleItem(ItemTransfer $itemTransfer): ItemCollectionTransfer
    {
        $configuredBundleQuantity = $itemTransfer->getConfiguredBundle()->getQuantity();
        if ($configuredBundleQuantity === 1) {
            return $this->createItemCollection($itemTransfer);
        }

        $configurableBundleItemCollection = new ItemCollectionTransfer();

        for ($index = 1; $index <= $configuredBundleQuantity; $index++) {
            $transformedItemTransfer = $this->transformItem($itemTransfer, $index);
            $itemCollection = $this->createItemCollection($transformedItemTransfer);

            foreach ($itemCollection->getItems() as $item) {
                $configurableBundleItemCollection->addItem($item);
            }
        }

        return $configurableBundleItemCollection;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param int $groupKeyIndex
     *
     * @return \Generated\Shared\Transfer\ItemTransfer
     */
    protected function transformItem(ItemTransfer $itemTransfer, int $groupKeyIndex): ItemTransfer
    {
        $transformedItemTransfer = new ItemTransfer();
        $transformedItemTransfer->fromArray($itemTransfer->toArray(), true);

        $groupKey = sprintf(
            '%s-%s',
            $itemTransfer->getConfiguredBundle()->getGroupKey(),
            $groupKeyIndex
        );

        $transformedItemTransfer->getConfiguredBundle()->setGroupKey($groupKey);
        $transformedItemTransfer->getConfiguredBundle()->setQuantity(1);
        $transformedItemTransfer->setQuantity(
            $itemTransfer->getConfiguredBundleItem()->getQuantityPerSlot()
        );

        return $transformedItemTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return \Generated\Shared\Transfer\ItemCollectionTransfer
     */
    protected function createItemCollection(ItemTransfer $itemTransfer): ItemCollectionTransfer
    {
        if ($this->isNonSplittableQuantityThresholdExceeded($itemTransfer)) {
            return (new ItemCollectionTransfer())->addItem($itemTransfer);
        }

        return $this->salesFacade->transformSplittableItem($itemTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return bool
     */
    protected function isNonSplittableQuantityThresholdExceeded(ItemTransfer $itemTransfer): bool
    {
        $threshold = $this->config->findConfigurableBundleItemQuantityThreshold();
        if ($threshold === null) {
            return false;
        }

        if ($itemTransfer->getQuantity() >= $threshold) {
            return true;
        }

        return false;
    }
}
