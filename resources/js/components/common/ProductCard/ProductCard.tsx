import { Button } from '@/components/ui/button';
import { cn, storageUrl } from '@/lib/utils';
import products, { show } from '@/routes/store/products';
import stockNotifications from '@/routes/store/stock-notifications';
import { useCartStore } from '@/store/cart.store';
import { useWishlistStore } from '@/store/wishlist.store';
import { Link, router } from '@inertiajs/react';
import {
    ArrowRight,
    Bell,
    Eye,
    Heart,
    Loader2,
    ShoppingBag,
    ShoppingCart,
    Star,
} from 'lucide-react';
import * as React from 'react';
import styles from './ProductCard.module.css';

type ProductProps = {
    product: App.Data.Basic.ProductData;
    handleAddToCart: () => void;
    handleToggleWishlist: () => void;
    handleSubscribeStockNotification: () => void;
    inWishlist: boolean;
    inCart: boolean;
    outOfStock: boolean;
    addToCartLoading: boolean;
    toggleWishlistLoading: boolean;
    subscribeStockNotificationLoading: boolean;
};

type BadgeProps = {
    badge?: { text: string; color: string };
};

const ProductLinkImage: React.FC<{
    product: App.Data.Basic.ProductData;
    className?: string;
    imageClassName?: string;
}> = ({ product, className, imageClassName }) => (
    <Link
        href={show.url(product.slug)}
        className={cn('block h-full w-full', className)}
    >
        <img
            src={storageUrl(product.defaultVariant!.defaultImage?.path)}
            alt={product.defaultVariant!.defaultImage?.altText ?? product.name}
            loading="lazy"
            className={cn('group-hover:scale-105', imageClassName)}
        />
    </Link>
);

const ProductBadge: React.FC<BadgeProps & { className?: string }> = ({
    badge,
    className,
}) => {
    if (!badge) return null;
    return <div className={cn(className, badge.color)}>{badge.text}</div>;
};

const ProductTitle: React.FC<{
    product: App.Data.Basic.ProductData;
    className?: string;
}> = ({ product, className }) => (
    <Link
        href={show.url(product.slug)}
        className="block"
    >
        <h3 className={cn('group-hover:text-primary', className)}>
            {product.name}
        </h3>
    </Link>
);

const WishlistIcon: React.FC<{
    inWishlist: boolean;
    className?: string;
    activeClassName?: string;
}> = ({ inWishlist, className, activeClassName }) => (
    <Heart
        className={cn(
            className,
            inWishlist &&
                'fill-red-500 text-red-500 ' + (activeClassName ?? ''),
        )}
    />
);

const QuickViewLink: React.FC<{
    product: App.Data.Basic.ProductData;
    className?: string;
    iconClassName?: string;
}> = ({ product, className, iconClassName }) => (
    <Link
        href={products.show.url(product.slug)}
        className={className}
    >
        <Eye className={iconClassName} />
    </Link>
);

const ProductRating: React.FC<{
    product: App.Data.Basic.ProductData;
    className?: string;
    starClassName?: string;
    starWrapperClassName?: string;
    valueClassName?: string;
    countClassName?: string;
    countText?: (reviews: number) => string;
}> = ({
    product,
    className,
    starClassName,
    starWrapperClassName,
    valueClassName,
    countClassName,
    countText,
}) => {
    if (product.reviews <= 0) {
        return null;
    }

    return (
        <div className={className}>
            {starWrapperClassName ? (
                <div className={starWrapperClassName}>
                    <Star className={starClassName} />
                </div>
            ) : (
                <Star className={starClassName} />
            )}
            <span className={valueClassName}>{product.rating}</span>
            <span className={countClassName}>
                {countText
                    ? countText(product.reviews)
                    : `(${product.reviews})`}
            </span>
        </div>
    );
};
export const ProductCard: React.FC<
    { product: App.Data.Basic.ProductData } & BadgeProps & {
            viewMode: 'grid' | 'list';
        }
> = ({ product, badge, viewMode }) => {
    const [addToCartLoading, setAddToCartLoading] = React.useState(false);
    const [toggleWishlistLoading, setToggleWishlistLoading] =
        React.useState(false);
    const [
        subscribeStockNotificationLoading,
        setSubscribeStockNotificationLoading,
    ] = React.useState(false);
    const inCart = useCartStore((state) =>
        state.inCart(product.defaultVariant.id),
    );
    const inWishlist = useWishlistStore((state) =>
        state.inWishlist(product.defaultVariant.id),
    );
    const outOfStock = product.defaultVariant.quantity == 0;
    const handleAddToCart = () => {
        if (product.variantsCount > 1) {
            router.visit('/cart');
        }
        router.post(
            `/cart/add/${product.defaultVariant.id}`,
            {},
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
                only: [],
                onStart: () => setAddToCartLoading(true),
                onFinish: () => setAddToCartLoading(false),
            },
        );
    };
    const handleToggleWishlist = () => {
        if (product.variantsCount > 1) {
            router.visit('/wishlist');
        }
        router.post(
            `/wishlist/toggle/${product.defaultVariant.id}`,
            {},
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
                only: [],
                onStart: () => setToggleWishlistLoading(true),
                onFinish: () => setToggleWishlistLoading(false),
            },
        );
    };
    const handleSubscribeStockNotification = () => {
        if (product.defaultVariant.quantity > 0) {
            return;
        }
        router.post(
            stockNotifications.store().url,
            {
                variant_id: product.defaultVariant.id,
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
                only: [],
                onStart: () => setSubscribeStockNotificationLoading(true),
                onFinish: () => setSubscribeStockNotificationLoading(false),
            },
        );
    };

    return viewMode === 'grid' ? (
        <ProductCardGridLayout
            product={product}
            badge={badge}
            handleAddToCart={handleAddToCart}
            handleToggleWishlist={handleToggleWishlist}
            handleSubscribeStockNotification={handleSubscribeStockNotification}
            inWishlist={inWishlist}
            inCart={inCart}
            outOfStock={outOfStock}
            addToCartLoading={addToCartLoading}
            subscribeStockNotificationLoading={
                subscribeStockNotificationLoading
            }
            toggleWishlistLoading={toggleWishlistLoading}
        />
    ) : (
        <ProductCardListLayout
            product={product}
            badge={badge}
            handleAddToCart={handleAddToCart}
            handleToggleWishlist={handleToggleWishlist}
            handleSubscribeStockNotification={handleSubscribeStockNotification}
            inWishlist={inWishlist}
            inCart={inCart}
            outOfStock={outOfStock}
            addToCartLoading={addToCartLoading}
            toggleWishlistLoading={toggleWishlistLoading}
            subscribeStockNotificationLoading={
                subscribeStockNotificationLoading
            }
        />
    );
};
const ProductCardGridLayout: React.FC<ProductProps & BadgeProps> = ({
    product,
    badge,
    handleAddToCart,
    handleToggleWishlist,
    inCart,
    inWishlist,
    outOfStock,
    addToCartLoading,
    toggleWishlistLoading,
    subscribeStockNotificationLoading,
    handleSubscribeStockNotification,
}) => {
    return (
        <div className={cn(styles.gridCard, 'group')}>
            {/* Product Image Area */}
            <div className={styles.gridImageContainer}>
                <ProductLinkImage
                    product={product}
                    className="pointer-events-auto"
                    imageClassName={styles.gridImage}
                />

                <ProductBadge
                    badge={badge}
                    className={styles.gridBadge}
                />

                {/* Quick Actions (Desktop Hover) */}
                <div className={styles.gridQuickActions}>
                    <Button
                        size="icon"
                        variant="secondary"
                        onClick={handleToggleWishlist}
                        disabled={toggleWishlistLoading}
                        className={styles.gridQuickActionButton}
                    >
                        {toggleWishlistLoading ? (
                            <Loader2 className="h-4 w-4 animate-spin text-primary" />
                        ) : (
                            <WishlistIcon
                                inWishlist={inWishlist}
                                className={cn(styles.gridQuickActionIcon)}
                                activeClassName={
                                    styles.gridQuickActionIconActive
                                }
                            />
                        )}
                    </Button>

                    <QuickViewLink
                        product={product}
                        className={styles.gridQuickViewLink}
                        iconClassName={styles.gridQuickViewIcon}
                    />
                </div>

                {/* Add to Cart Overlay (Desktop) */}
                <div className={styles.gridAddToCartOverlay}>
                    {outOfStock ? (
                        <Button
                            disabled={subscribeStockNotificationLoading}
                            onClick={() => handleSubscribeStockNotification()}
                            className={cn(
                                styles.notificationButton,
                                'bg-amber-600 hover:bg-amber-700',
                            )}
                            size="sm"
                        >
                            {subscribeStockNotificationLoading ? (
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
                    ) : !inCart ? (
                        <Button
                            onClick={handleAddToCart}
                            disabled={addToCartLoading}
                            className={styles.gridAddToCartButton}
                            size="default"
                        >
                            {addToCartLoading ? (
                                <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                            ) : (
                                <ShoppingCart
                                    className={styles.gridAddToCartIcon}
                                />
                            )}
                            أضف للسلة
                        </Button>
                    ) : (
                        <Button
                            onClick={() => router.visit('/cart')}
                            className={styles.gridAddToCartButton}
                            size="default"
                        >
                            <ArrowRight className={styles.gridAddToCartIcon} />
                            الذهاب للسلة
                        </Button>
                    )}
                </div>

                {/* Mobile Wishlist */}
                <button
                    type="button"
                    onClick={(e) => {
                        e.preventDefault();
                        handleToggleWishlist();
                    }}
                    disabled={toggleWishlistLoading}
                    className={styles.gridMobileWishlistButton}
                >
                    {toggleWishlistLoading ? (
                        <Loader2 className="h-4 w-4 animate-spin text-gray-500" />
                    ) : (
                        <WishlistIcon
                            inWishlist={inWishlist}
                            className={cn(styles.gridMobileWishlistIcon)}
                            activeClassName={
                                styles.gridMobileWishlistIconActive
                            }
                        />
                    )}
                </button>
            </div>

            {/* Product Info */}
            <div className={styles.gridProductInfo}>
                <ProductTitle
                    product={product}
                    className={styles.gridProductName}
                />

                {product.description && (
                    <p className={styles.gridDescription}>
                        {product.description}
                    </p>
                )}

                <div className={styles.gridSpacer}>
                    {/* Rating */}
                    <ProductRating
                        product={product}
                        className={styles.gridRating}
                        starClassName={styles.gridRatingStar}
                        valueClassName={styles.gridRatingValue}
                        countClassName={styles.gridRatingCount}
                    />

                    {/* Price & Action (Mobile) */}
                    <div className={styles.gridPriceContainer}>
                        <div className={styles.gridPriceWrapper}>
                            <span className={styles.gridPrice}>
                                {product.defaultVariant?.price}
                            </span>
                            {product.defaultVariant?.compareAtPrice && (
                                <span className={styles.gridComparePrice}>
                                    {product.defaultVariant?.compareAtPrice}
                                </span>
                            )}
                        </div>

                        {/* Mobile Add Button */}
                        <Button
                            size="icon"
                            className={styles.gridMobileAddButton}
                            variant={inCart ? 'default' : 'secondary'}
                            disabled={outOfStock || addToCartLoading}
                            onClick={
                                inCart
                                    ? () => router.visit('/cart')
                                    : handleAddToCart
                            }
                        >
                            {addToCartLoading ? (
                                <Loader2 className="h-4 w-4 animate-spin" />
                            ) : inCart ? (
                                <ArrowRight className="h-4 w-4" />
                            ) : (
                                <ShoppingCart
                                    className={styles.gridMobileAddIcon}
                                />
                            )}
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    );
};

const ProductCardListLayout: React.FC<ProductProps & BadgeProps> = ({
    product,
    badge,
    handleAddToCart,
    handleToggleWishlist,
    handleSubscribeStockNotification,
    inCart,
    inWishlist,
    outOfStock,
    addToCartLoading,
    toggleWishlistLoading,
    subscribeStockNotificationLoading,
}) => {
    return (
        <div className={cn(styles.listCard, 'group')}>
            {/* Image Section */}
            <div className={styles.listImageContainer}>
                <ProductLinkImage
                    product={product}
                    imageClassName={styles.listImage}
                />

                <ProductBadge
                    badge={badge}
                    className={styles.listBadge}
                />

                {!badge && product.defaultVariant?.compareAtPrice && (
                    <div className={styles.listDiscountBadge}>خصم</div>
                )}
            </div>

            {/* Content Section */}
            <div className={styles.listContent}>
                <div>
                    <div className={styles.listHeader}>
                        <ProductTitle
                            product={product}
                            className={styles.listProductName}
                        />

                        <Button
                            size="icon"
                            variant="ghost"
                            onClick={handleToggleWishlist}
                            disabled={toggleWishlistLoading}
                            className={styles.listWishlistButton}
                        >
                            {toggleWishlistLoading ? (
                                <Loader2 className="h-4 w-4 animate-spin" />
                            ) : (
                                <WishlistIcon
                                    inWishlist={inWishlist}
                                    className={cn(styles.listWishlistIcon)}
                                    activeClassName={
                                        styles.listWishlistIconActive
                                    }
                                />
                            )}
                        </Button>
                    </div>

                    {/* Rating */}
                    <ProductRating
                        product={product}
                        className={styles.listRating}
                        starClassName={styles.listRatingStar}
                        starWrapperClassName="flex text-amber-400"
                        valueClassName={styles.listRatingValue}
                        countClassName={styles.listRatingCount}
                        countText={(reviews) => `(${reviews} تقييم)`}
                    />

                    {product.description && (
                        <p className={styles.listDescription}>
                            {product.description}
                        </p>
                    )}
                </div>

                {/* Footer: Price & Actions */}
                <div className={styles.listFooter}>
                    <div className={styles.listPriceWrapper}>
                        {product.defaultVariant?.compareAtPrice && (
                            <span className={styles.listComparePrice}>
                                {product.defaultVariant?.compareAtPrice}
                            </span>
                        )}
                        <span className={styles.listPrice}>
                            {product.defaultVariant?.price}
                        </span>
                    </div>

                    <div className={styles.listActions}>
                        {/* Mobile Add Button */}
                        <Button
                            size="icon"
                            className={styles.listMobileAddButton}
                            disabled={outOfStock || addToCartLoading}
                            onClick={
                                inCart
                                    ? () => router.visit('/cart')
                                    : handleAddToCart
                            }
                        >
                            {addToCartLoading ? (
                                <Loader2 className="h-4 w-4 animate-spin" />
                            ) : inCart ? (
                                <ArrowRight className="h-4 w-4" />
                            ) : (
                                <ShoppingCart
                                    className={styles.listMobileAddIcon}
                                />
                            )}
                        </Button>

                        {/* Desktop Add Button */}
                        {outOfStock ? (
                            <Button
                                className={cn(
                                    styles.listDesktopAddButton,
                                    subscribeStockNotificationLoading &&
                                        'cursor-not-allowed opacity-70',
                                )}
                                size="sm"
                                variant="secondary"
                                onClick={handleSubscribeStockNotification}
                                disabled={subscribeStockNotificationLoading}
                            >
                                {subscribeStockNotificationLoading ? (
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
                        ) : inCart ? (
                            <Button
                                className={styles.listDesktopAddButton}
                                size="sm"
                                onClick={() => router.visit('/cart')}
                            >
                                <ArrowRight
                                    className={styles.listDesktopAddIcon}
                                />
                                الذهاب للسلة
                            </Button>
                        ) : (
                            <Button
                                className={styles.listDesktopAddButton}
                                size="sm"
                                onClick={handleAddToCart}
                                disabled={addToCartLoading}
                            >
                                {addToCartLoading ? (
                                    <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                                ) : (
                                    <ShoppingBag
                                        className={styles.listDesktopAddIcon}
                                    />
                                )}
                                أضف للسلة
                            </Button>
                        )}

                        <QuickViewLink
                            product={product}
                            className={cn(
                                'inline-flex h-9 w-9 items-center justify-center rounded-md border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground',
                                styles.listQuickViewButton,
                            )}
                            iconClassName={styles.listQuickViewIcon}
                        />
                    </div>
                </div>
            </div>
        </div>
    );
};
