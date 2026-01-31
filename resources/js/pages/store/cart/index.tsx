import { SkeletonWrapper } from '@/components/feedback/SkeletonWrapper';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import StoreLayout from '@/layouts/StoreLayout';
import { cn, storageUrl } from '@/lib/utils';
import checkoutRoutes from '@/routes/store/checkout';
import stockNotificationsRoutes from '@/routes/store/stock-notifications';
import { Head, Link, router, usePage } from '@inertiajs/react';
import {
    Bell,
    Loader2,
    Minus,
    Package,
    Plus,
    ShoppingCart,
    Trash2,
} from 'lucide-react';
import * as React from 'react';
import { styles } from './index.styles';
export default function CartPage() {
    const { cart } = usePage<{ cart: App.Data.Basic.CartData }>().props;
    // Loading states
    const [isLoading, setIsLoading] = React.useState<boolean>(false);
    // --- Helper Function to handle Router Logic reuse ---
    const executeCartAction = (
        method: 'patch' | 'delete' | 'post',
        url: string,
        payload: any = {},
    ) => {
        router[method](url, payload, {
            preserveScroll: true,
            replace: true,
            showProgress: false,
            onStart: () => {
                setIsLoading(true);
            },
            onFinish: () => {
                setIsLoading(false);
            },
        });
    };
    // ----------------------------------------------------

    const handleIncrease = (item: App.Data.Basic.CartItemData) => {
        executeCartAction(
            'patch',
            `/cart/items/${item.productVariant.id}/quantity`,
            {
                quantity: item.quantity + 1,
            },
        );
    };

    const handleDecrease = (item: App.Data.Basic.CartItemData) => {
        if (item.quantity <= 1) return;
        executeCartAction(
            'patch',
            `/cart/items/${item.productVariant.id}/quantity`,
            {
                quantity: item.quantity - 1,
            },
        );
    };

    const handleRemove = (item: App.Data.Basic.CartItemData) => {
        if (!confirm('حذف المنتج من السلة؟')) return;
        executeCartAction('delete', `/cart/items/${item.id}`);
    };

    const handleToggleSelection = (itemId: number) => {
        executeCartAction('patch', `/cart/items/${itemId}/toggle-selection`);
    };

    const handleToggleAll = (checked: boolean) => {
        executeCartAction('patch', '/cart/toggle-all', { select_all: checked });
    };

    const handleSubscribeStockNotification = (
        item: App.Data.Basic.CartItemData,
    ) => {
        executeCartAction('post', stockNotificationsRoutes.store().url, {
            variant_id: item.productVariant.id,
        });
    };

    if (!cart) {
        return (
            <StoreLayout>
                <Head title="سلة التسوق" />
                <div className={styles.container}>
                    <SkeletonWrapper name="cartPage" />
                </div>
            </StoreLayout>
        );
    }

    return (
        <StoreLayout>
            <Head title="سلة التسوق" />
            <div className={styles.container}>
                {cart.items.length === 0 ? (
                    <div className={styles.emptyContainer}>
                        <div className={styles.emptyIconWrapper}>
                            <ShoppingCart className={styles.emptyIcon} />
                        </div>
                        <h2 className={styles.emptyTitle}>السلة فارغة</h2>
                        <Link href="/">
                            <Button className={styles.shopButton}>
                                ابدأ التسوق
                            </Button>
                        </Link>
                    </div>
                ) : (
                    <div className={styles.content}>
                        <div className={styles.itemsCard}>
                            {/* Header / Select All */}
                            <div className={styles.cardHeader}>
                                <div className={styles.cardHeaderStart}>
                                    <div className={styles.selectAllButton}>
                                        <Checkbox
                                            checked={cart.isAllSelected}
                                            onCheckedChange={(checked) =>
                                                handleToggleAll(!!checked)
                                            }
                                            disabled={isLoading}
                                            className={styles.checkbox}
                                        />
                                    </div>
                                    <div className={styles.cardTitleGroup}>
                                        <h1 className={styles.cardTitle}>
                                            سلة التسوق
                                        </h1>
                                    </div>
                                </div>
                                {cart.selectedCount > 0 && (
                                    <span className={styles.selectedBadge}>
                                        {cart.selectedCount} محدد
                                    </span>
                                )}
                            </div>

                            {/* Items */}
                            <div className={styles.itemsList}>
                                {cart.items.map((item) => (
                                    <CartItemCard
                                        key={item.id}
                                        item={item}
                                        handleRemove={handleRemove}
                                        handleIncrease={handleIncrease}
                                        handleDecrease={handleDecrease}
                                        handleSubscribeStockNotification={
                                            handleSubscribeStockNotification
                                        }
                                        toggleItem={handleToggleSelection}
                                        isLoading={isLoading}
                                    />
                                ))}
                            </div>
                        </div>

                        <CartSummary
                            cart={cart}
                            isLoading={isLoading}
                        />
                    </div>
                )}
            </div>
        </StoreLayout>
    );
}

function CartItemCard({
    item,
    handleRemove,
    toggleItem,
    handleIncrease,
    handleDecrease,
    handleSubscribeStockNotification,
    isLoading,
}: {
    item: App.Data.Basic.CartItemData;
    handleRemove: (item: App.Data.Basic.CartItemData) => void;
    toggleItem: (itemId: number) => void;
    handleIncrease: (item: App.Data.Basic.CartItemData) => void;
    handleDecrease: (item: App.Data.Basic.CartItemData) => void;
    handleSubscribeStockNotification: (
        item: App.Data.Basic.CartItemData,
    ) => void;
    isLoading: boolean;
}) {
    const isOutOfStock = item.productVariant.quantity === 0;
    const productLink = `/products/${item.product.slug}`;

    return (
        <div
            className={cn(styles.item, item.isSelected && styles.itemSelected)}
        >
            <button
                onClick={() => handleRemove(item)}
                disabled={isLoading}
                className={cn(
                    styles.deleteButton,
                    isLoading && 'cursor-not-allowed opacity-50',
                )}
            >
                <Trash2 className={styles.deleteIcon} />
            </button>

            <div className={styles.itemCheckbox}>
                <Checkbox
                    checked={item.isSelected}
                    onCheckedChange={() => toggleItem(item.id)}
                    disabled={item.productVariant.quantity === 0 || isLoading}
                    className={styles.checkbox}
                />
            </div>

            <Link
                href={productLink}
                className={styles.itemImageLink}
            >
                <img
                    src={storageUrl(item.productVariant.defaultImage?.path)}
                    alt={item.product.name}
                    loading="lazy"
                    decoding="async"
                    className={styles.itemImage}
                />
            </Link>

            <div className={styles.itemInfo}>
                <Link
                    href={productLink}
                    className={styles.itemName}
                >
                    {item.product.name}
                </Link>
                {item.productVariant.attributes.length > 0 && (
                    <div className={styles.itemAttributes}>
                        {item.productVariant.attributes.map((attribute) => (
                            <Badge
                                key={`${attribute.attributeId}-${attribute.valueId}`}
                                variant="outline"
                                className={styles.itemAttributeBadge}
                            >
                                {attribute.attributeType === 2 &&
                                    attribute.colorCode && (
                                        <span
                                            className={
                                                styles.itemAttributeColor
                                            }
                                            style={{
                                                backgroundColor:
                                                    attribute.colorCode,
                                            }}
                                        />
                                    )}
                                <span>
                                    {attribute.attributeName}:{' '}
                                    {attribute.valueName}
                                </span>
                            </Badge>
                        ))}
                    </div>
                )}
                <div className={styles.itemPriceRow}>
                    <span
                        className={cn(
                            styles.itemUnitPrice,
                            isLoading && 'opacity-50',
                        )}
                    >
                        {item.productVariant.price} ر.س
                    </span>
                </div>
            </div>

            <div className={styles.itemTotalColumn}>
                <div
                    className={cn(styles.itemTotal, isLoading && 'opacity-50')}
                >
                    {(
                        parseFloat(item.productVariant.price) * item.quantity
                    ).toFixed(2)}{' '}
                    ر.س
                </div>

                {isOutOfStock ? (
                    <Button
                        onClick={() => handleSubscribeStockNotification(item)}
                        disabled={isLoading}
                        className={cn(
                            styles.notificationButton,
                            'bg-amber-600 hover:bg-amber-700',
                        )}
                        size="sm"
                    >
                        {isLoading ? (
                            <>
                                <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                                جاري الاشتراك...
                            </>
                        ) : (
                            <>
                                <Bell className="mr-2 h-4 w-4" />
                                إشعار عند التوفر
                            </>
                        )}
                    </Button>
                ) : (
                    <div className={styles.quantityControl}>
                        <Button
                            size="icon"
                            variant="outline"
                            className={styles.quantityButton}
                            onClick={() => handleDecrease(item)}
                            disabled={item.quantity <= 1 || isLoading}
                        >
                            <Minus className={styles.quantityIcon} />
                        </Button>
                        <span className={styles.quantityValue}>
                            {item.quantity}
                        </span>
                        <Button
                            size="icon"
                            variant="outline"
                            className={styles.quantityButton}
                            onClick={() => handleIncrease(item)}
                            disabled={isLoading}
                        >
                            <Plus className={styles.quantityIcon} />
                        </Button>
                    </div>
                )}
            </div>
        </div>
    );
}

function CartSummary({
    cart,
    isLoading,
}: {
    cart: App.Data.Basic.CartData;
    isLoading: boolean;
}) {
    return (
        <div className={styles.summarySection}>
            <div className={styles.summaryCard}>
                <h2 className={styles.summaryTitle}>ملخص الطلب</h2>

                <div className={styles.summaryInfo}>
                    <Package className={styles.summaryInfoIcon} />
                    <span>{cart.selectedCount} عنصر محدد</span>
                </div>
                <div className={styles.summaryDivider} />
                <div
                    className={cn(styles.summaryRow, isLoading && 'opacity-70')}
                >
                    <span>المجموع الفرعي</span>
                    <span>{cart.formattedSubtotal}</span>
                </div>
                <div className={styles.summaryDivider} />
                <div
                    className={cn(
                        styles.summaryRow,
                        styles.summaryTotal,
                        isLoading && 'opacity-70',
                    )}
                >
                    <span>المجموع الكلي</span>
                    <span className={styles.totalPrice}>
                        {cart.formattedSubtotal}
                    </span>
                </div>

                <Button
                    className={styles.checkoutButton}
                    size="lg"
                    disabled={cart.selectedCount === 0 || isLoading}
                    onClick={() => router.visit(checkoutRoutes.index().url)}
                >
                    {isLoading ? (
                        <>
                            <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                            جاري التحديث...
                        </>
                    ) : (
                        'إتمام الطلب'
                    )}
                </Button>
            </div>
        </div>
    );
}
