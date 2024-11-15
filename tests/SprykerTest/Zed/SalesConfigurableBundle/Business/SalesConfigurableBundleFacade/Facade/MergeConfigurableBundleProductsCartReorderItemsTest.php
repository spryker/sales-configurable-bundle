<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\SalesConfigurableBundle\Business\SalesConfigurableBundleFacade\Facade;

use Codeception\Test\Unit;
use Generated\Shared\DataBuilder\CartReorderBuilder;
use Generated\Shared\DataBuilder\ItemBuilder;
use Generated\Shared\Transfer\CartReorderRequestTransfer;
use Generated\Shared\Transfer\CartReorderTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\SalesOrderConfiguredBundleTransfer;
use Spryker\Shared\Kernel\Transfer\Exception\NullValueException;
use SprykerTest\Zed\SalesConfigurableBundle\SalesConfigurableBundleBusinessTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group SalesConfigurableBundle
 * @group Business
 * @group SalesConfigurableBundleFacade
 * @group Facade
 * @group MergeConfigurableBundleProductsCartReorderItemsTest
 * Add your own group annotations below this line
 */
class MergeConfigurableBundleProductsCartReorderItemsTest extends Unit
{
    /**
     * @var string
     */
    protected const TEST_ITEM_GROUP_KEY = 'test-item-group-key';

    /**
     * @var \SprykerTest\Zed\SalesConfigurableBundle\SalesConfigurableBundleBusinessTester
     */
    protected SalesConfigurableBundleBusinessTester $tester;

    /**
     * @return void
     */
    public function testMergesConfigurableBundleItemsQuantity(): void
    {
        // Arrange
        $cartReorderTransfer = (new CartReorderBuilder([
            CartReorderTransfer::ORDER => [
                OrderTransfer::ITEMS => [
                    $this->createOrderItemTransfer(1)->toArray(),
                    $this->createOrderItemTransfer(2)->toArray(),
                ],
            ],
            CartReorderTransfer::ORDER_ITEMS => [
                $this->createOrderItemTransfer(1)->toArray(),
                $this->createOrderItemTransfer(2)->toArray(),
            ],
        ]))->build();
        $cartReorderRequestTransfer = new CartReorderRequestTransfer();

        // Act
        $cartReorderTransfer = $this->tester->getFacade()->mergeConfigurableBundleProductsCartReorderItems(
            $cartReorderRequestTransfer,
            $cartReorderTransfer,
        );

        // Assert
        $this->assertCount(1, $cartReorderTransfer->getOrderItems());
        $this->assertCount(2, $cartReorderTransfer->getOrderOrFail()->getItems());

        $orderItemTransfer = $cartReorderTransfer->getOrderItems()->getIterator()->current();
        $this->assertNotNull($orderItemTransfer->getSalesOrderConfiguredBundle());
        $this->assertSame(2, $orderItemTransfer->getQuantity());
        $this->assertSame(2, $orderItemTransfer->getSalesOrderConfiguredBundleOrFail()->getQuantity());
    }

    /**
     * @return void
     */
    public function testDoesNothingWhenNoItemsWithSalesConfiguredBundleProvided(): void
    {
        // Arrange
        $cartReorderTransfer = (new CartReorderBuilder([
            CartReorderTransfer::ORDER => [
                OrderTransfer::ITEMS => [
                    [ItemTransfer::CONFIGURED_BUNDLE => null],
                    [ItemTransfer::CONFIGURED_BUNDLE => null],
                ],
            ],
            CartReorderTransfer::ORDER_ITEMS => [
                [ItemTransfer::CONFIGURED_BUNDLE => null],
                [ItemTransfer::CONFIGURED_BUNDLE => null],
            ],
        ]))->build();

        // Act
        $cartReorderRequestTransfer = new CartReorderRequestTransfer();

        // Act
        $cartReorderTransfer = $this->tester->getFacade()->mergeConfigurableBundleProductsCartReorderItems(
            $cartReorderRequestTransfer,
            $cartReorderTransfer,
        );

        // Assert
        $this->assertCount(2, $cartReorderTransfer->getOrderItems());
        $this->assertCount(2, $cartReorderTransfer->getOrderOrFail()->getItems());
    }

    /**
     * @return void
     */
    public function testDoesNothingWhenItemsWithSalesConfiguredBundleNotRequestedInCartReorderRequestTransfer(): void
    {
        // Arrange
        $cartReorderTransfer = (new CartReorderBuilder([
            CartReorderTransfer::ORDER => [
                OrderTransfer::ITEMS => [
                    $this->createOrderItemTransfer(1)->toArray(),
                    [
                        ItemTransfer::ID_SALES_ORDER_ITEM => 2,
                        ItemTransfer::CONFIGURED_BUNDLE => null,
                    ],
                ],
            ],
            CartReorderTransfer::ORDER_ITEMS => [
                [
                    ItemTransfer::ID_SALES_ORDER_ITEM => 2,
                    ItemTransfer::CONFIGURED_BUNDLE => null,
                ],
            ],
        ]))->build();
        $cartReorderRequestTransfer = (new CartReorderRequestTransfer())->addIdSalesOrderItem(2);

        // Act
        $cartReorderTransfer = $this->tester->getFacade()->mergeConfigurableBundleProductsCartReorderItems(
            $cartReorderRequestTransfer,
            $cartReorderTransfer,
        );

        // Assert
        $this->assertCount(1, $cartReorderTransfer->getOrderItems());
        $this->assertCount(2, $cartReorderTransfer->getOrderOrFail()->getItems());

        $orderItemTransfer = $cartReorderTransfer->getOrderItems()->getIterator()->current();
        $this->assertNull($orderItemTransfer->getAmountSalesUnit());
        $this->assertSame(2, $orderItemTransfer->getIdSalesOrderItem());
    }

    /**
     * @dataProvider throwsNullValueExceptionWhenRequiredItemPropertyIsNotSetDataProvider
     *
     * @param \Generated\Shared\Transfer\CartReorderTransfer $cartReorderTransfer
     * @param string $exceptionMessage
     *
     * @return void
     */
    public function testThrowsNullValueExceptionWhenRequiredItemPropertyIsNotSet(CartReorderTransfer $cartReorderTransfer, string $exceptionMessage): void
    {
        // Assert
        $this->expectException(NullValueException::class);
        $this->expectExceptionMessage($exceptionMessage);

        // Act
        $this->tester->getFacade()->mergeConfigurableBundleProductsCartReorderItems(
            new CartReorderRequestTransfer(),
            $cartReorderTransfer,
        );
    }

    /**
     * @return array<string, list<\Generated\Shared\Transfer\CartReorderTransfer|string>>
     */
    protected function throwsNullValueExceptionWhenRequiredItemPropertyIsNotSetDataProvider(): array
    {
        return [
            'order is not provided' => [
                (new CartReorderBuilder([
                    CartReorderTransfer::ORDER => null,
                ]))->withOrderItem($this->createOrderItemTransfer(1)->toArray())
                    ->build(),
                sprintf('Property "order" of transfer `%s` is null.', CartReorderTransfer::class),
            ],
            'group key is not provided' => [
                (new CartReorderBuilder())
                    ->withOrder([
                        OrderTransfer::ITEMS => [
                            [
                                ItemTransfer::GROUP_KEY => null,
                                ItemTransfer::ID_SALES_ORDER_ITEM => 1,
                                ItemTransfer::QUANTITY => 1,
                                ItemTransfer::SALES_ORDER_CONFIGURED_BUNDLE_ITEM => [],
                                ItemTransfer::SALES_ORDER_CONFIGURED_BUNDLE => [],
                            ],
                        ],
                    ])
                    ->withOrderItem($this->createOrderItemTransfer(1)->toArray())
                    ->build(),
                sprintf('Property "groupKey" of transfer `%s` is null.', ItemTransfer::class),
            ],
            'quantity is not provided' => [
                (new CartReorderBuilder())
                    ->withOrder([
                        OrderTransfer::ITEMS => [
                            [
                                ItemTransfer::QUANTITY => null,
                                ItemTransfer::GROUP_KEY => static::TEST_ITEM_GROUP_KEY,
                                ItemTransfer::ID_SALES_ORDER_ITEM => 1,
                                ItemTransfer::SALES_ORDER_CONFIGURED_BUNDLE_ITEM => [],
                                ItemTransfer::SALES_ORDER_CONFIGURED_BUNDLE => [],
                            ],
                        ],
                    ])
                    ->withOrderItem($this->createOrderItemTransfer(1)->toArray())
                    ->build(),
                sprintf('Property "quantity" of transfer `%s` is null.', ItemTransfer::class),
            ],
            'idSalesOrderItem is not provided for item in order' => [
                (new CartReorderBuilder())
                    ->withOrder([
                        OrderTransfer::ITEMS => [
                            [
                                ItemTransfer::GROUP_KEY => static::TEST_ITEM_GROUP_KEY,
                                ItemTransfer::QUANTITY => 1,
                                ItemTransfer::ID_SALES_ORDER_ITEM => null,
                                ItemTransfer::SALES_ORDER_CONFIGURED_BUNDLE_ITEM => [],
                                ItemTransfer::SALES_ORDER_CONFIGURED_BUNDLE => [],
                            ],
                        ],
                    ])
                    ->withOrderItem($this->createOrderItemTransfer(1)->toArray())
                    ->build(),
                sprintf('Property "idSalesOrderItem" of transfer `%s` is null.', ItemTransfer::class),
            ],
            'idSalesOrderItem is not provided for order item' => [
                (new CartReorderBuilder())
                    ->withOrder([
                        OrderTransfer::ITEMS => [$this->createOrderItemTransfer(1)->toArray()],
                    ])
                    ->withOrderItem([
                        ItemTransfer::ID_SALES_ORDER_ITEM => null,
                    ])
                    ->build(),
                sprintf('Property "idSalesOrderItem" of transfer `%s` is null.', ItemTransfer::class),
            ],
        ];
    }

    /**
     * @param int $idSalesOrderItem
     *
     * @return \Generated\Shared\Transfer\ItemTransfer
     */
    protected function createOrderItemTransfer(int $idSalesOrderItem): ItemTransfer
    {
        return (new ItemBuilder([
            ItemTransfer::GROUP_KEY => static::TEST_ITEM_GROUP_KEY,
            ItemTransfer::QUANTITY => 1,
            ItemTransfer::ID_SALES_ORDER_ITEM => $idSalesOrderItem,
        ]))->withSalesOrderConfiguredBundle([
            SalesOrderConfiguredBundleTransfer::QUANTITY => 1,
        ])->withSalesOrderConfiguredBundleItem()
            ->build();
    }
}
