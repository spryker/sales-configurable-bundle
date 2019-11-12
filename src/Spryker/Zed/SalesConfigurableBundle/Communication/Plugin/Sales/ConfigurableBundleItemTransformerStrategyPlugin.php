<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SalesConfigurableBundle\Communication\Plugin\Sales;

use Generated\Shared\Transfer\ItemCollectionTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\SalesExtension\Dependency\Plugin\ItemTransformerStrategyPluginInterface;

/**
 * @method \Spryker\Zed\SalesConfigurableBundle\Business\SalesConfigurableBundleFacadeInterface getFacade()
 * @method \Spryker\Zed\SalesConfigurableBundle\SalesConfigurableBundleConfig getConfig()
 * @method \Spryker\Zed\SalesConfigurableBundle\Communication\SalesConfigurableBundleCommunicationFactory getFactory()
 */
class ConfigurableBundleItemTransformerStrategyPlugin extends AbstractPlugin implements ItemTransformerStrategyPluginInterface
{
    /**
     * {@inheritDoc}
     * - Checks if given item is part of configurable bundle.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return bool
     */
    public function isApplicable(ItemTransfer $itemTransfer): bool
    {
        return $itemTransfer->getConfiguredBundle() !== null;
    }

    /**
     * {@inheritDoc}
     * - Splits configurable bundles.
     * - Transforms items in configurable bundle depending on configurable bundle item threshold.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return \Generated\Shared\Transfer\ItemCollectionTransfer
     */
    public function transformItem(ItemTransfer $itemTransfer): ItemCollectionTransfer
    {
        return $this->getFacade()->transformConfigurableBundleItem($itemTransfer);
    }
}