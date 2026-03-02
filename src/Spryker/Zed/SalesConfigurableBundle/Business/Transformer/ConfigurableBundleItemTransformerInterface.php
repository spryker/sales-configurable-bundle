<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SalesConfigurableBundle\Business\Transformer;

use Generated\Shared\Transfer\OrderTransfer;

interface ConfigurableBundleItemTransformerInterface
{
    public function transformConfiguredBundleOrderItems(OrderTransfer $orderTransfer): OrderTransfer;
}
