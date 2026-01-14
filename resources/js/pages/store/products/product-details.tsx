import { ImageGallery } from '@/components/common/image-gallery';
import { RatingStars } from '@/components/common/review-card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { PriceSection } from '@/features/productDetails/price-section';
import { DetailsTabsSection } from '@/features/productDetails/product-detail-tabs-section';
import { RelatedProductsSection } from '@/features/productDetails/related-products-section';
import { StockStatusLine } from '@/features/productDetails/stock-status-line';
import StoreLayout from '@/layouts/StoreLayout';
import { cn } from '@/lib/utils';
import brandRoutes from '@/routes/store/brand';
import productsRoutes from '@/routes/store/products';
import { useCartStore } from '@/store/cart.store';
import { useWishlistStore } from '@/store/wishlist.store';
import { Head, Link, router, usePage } from '@inertiajs/react';
import {
    ArrowRight,
    Copy,
    Heart,
    Loader2,
    Minus,
    Plus,
    Shield,
    ShoppingCart,
    TrendingUp,
    Truck,
} from 'lucide-react';
import * as React from 'react';
export default function ProductDetails() {
    const { product } = usePage<{
        product: App.Data.Basic.ProductDetailsData;
    }>().props;
    const selectedVariant = product.variant;

    return (
        <StoreLayout>
            <Head title={`${product.name}`} />
            <section className="bg-gray-50/30 py-6 md:py-8 dark:bg-background">
                <div className="container mx-auto max-w-6xl px-4">
                    <div className="grid items-start gap-8 md:grid-cols-2">
                        {/* Column 1: Gallery */}
                        <div className="w-full">
                            <div className="sticky top-20">
                                <ImageGallery
                                    key={selectedVariant.id}
                                    images={selectedVariant.images || []}
                                />
                            </div>
                        </div>

                        {/* Column 2: Info & Actions */}
                        <div className="flex flex-col gap-5">
                            {/* Product Header */}
                            <div className="space-y-3">
                                <div className="flex items-start justify-between">
                                    <div className="space-y-1">
                                        {product.brand && (
                                            <Link
                                                href={brandRoutes.show.url(
                                                    product.brand.slug,
                                                )}
                                                className="text-xs font-semibold tracking-wide text-primary uppercase"
                                            >
                                                {product.brand.name}
                                            </Link>
                                        )}
                                        <h1 className="text-2xl leading-tight font-bold text-foreground md:text-3xl">
                                            {product.name}
                                        </h1>
                                    </div>
                                    <div className="flex items-center gap-1 rounded border bg-muted/50 px-2 py-1 text-[10px] text-muted-foreground">
                                        <span className="font-mono">
                                            {selectedVariant.sku}
                                        </span>
                                        <Copy
                                            className="h-3 w-3 cursor-pointer hover:text-foreground"
                                            onClick={() =>
                                                navigator.clipboard.writeText(
                                                    selectedVariant.sku,
                                                )
                                            }
                                        />
                                    </div>
                                </div>

                                <div className="flex items-center gap-3 text-sm">
                                    <div className="flex items-center gap-1 text-yellow-500">
                                        <RatingStars rating={product.rating} />
                                        <span className="mr-1 font-medium text-foreground">
                                            {product.rating}
                                        </span>
                                    </div>
                                    <span className="text-xs text-muted-foreground">
                                        ({product.reviews} reviews)
                                    </span>
                                    <Badge
                                        variant="secondary"
                                        className="gap-1 bg-green-100 text-[10px] text-green-700"
                                    >
                                        <TrendingUp className="h-3 w-3" /> Best
                                        Seller
                                    </Badge>
                                </div>
                            </div>

                            <Separator />

                            {/* Actions Component */}
                            <ProductActions product={product} />

                            {/* Features Grid */}
                            <div className="grid grid-cols-2 gap-3 pt-2">
                                <div className="flex items-center gap-2 rounded border border-transparent bg-muted/40 p-2">
                                    <div className="rounded-full bg-background p-1.5 text-blue-600 shadow-sm">
                                        <Truck className="h-3.5 w-3.5" />
                                    </div>
                                    <span className="text-xs font-medium text-muted-foreground">
                                        Fast Shipping
                                    </span>
                                </div>
                                <div className="flex items-center gap-2 rounded border border-transparent bg-muted/40 p-2">
                                    <div className="rounded-full bg-background p-1.5 text-purple-600 shadow-sm">
                                        <Shield className="h-3.5 w-3.5" />
                                    </div>
                                    <span className="text-xs font-medium text-muted-foreground">
                                        2 Years Warranty
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <DetailsTabsSection productDetails={product} />

                    <RelatedProductsSection />
                </div>
            </section>
        </StoreLayout>
    );
}

interface Props {
    product: App.Data.Basic.ProductDetailsData;
}

export function ProductActions({ product }: Props) {
    const selectedVariant = product.variant;
    const inCart = useCartStore((state) => state.inCart(selectedVariant.id));
    const inWishlist = useWishlistStore((state) =>
        state.inWishlist(selectedVariant.id),
    );
    const cartQuantity = useCartStore((state) =>
        state.getQuantity(selectedVariant.id),
    );
    // --- Local State ---
    const [isLoading, setIsLoading] = React.useState(false);
    const isOutOfStock = selectedVariant.quantity === 0;
    const displayQuantity = inCart ? cartQuantity : 1;
    // 1. Helper for Inertia Requests
    const executeAction = (
        method: 'post' | 'patch' | 'delete',
        url: string,
        data: Record<string, any> = {},
    ) => {
        if (isLoading) return;
        router[method](url, data, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            only: [],
            onStart: () => setIsLoading(true),
            onFinish: () => setIsLoading(false),
        });
    };

    // 2. Change Filters (Using your logic)
    const handleAttributeChange = (attrId: number, valId: number) => {
        // Calculate new filters as requested
        const currentSelectedValues = product.attributes
            .map((attr) =>
                attr.id === attrId ? valId : attr.selectedValue?.id,
            )
            .filter(Boolean);
        router.get(
            productsRoutes.show(product.slug),
            {
                filters: {
                    values: currentSelectedValues,
                },
            },
            {
                preserveScroll: true,
                replace: true,
            },
        );
    };

    // 3. Add to Cart
    const handleAddToCart = () => {
        executeAction('post', `/cart/add/${selectedVariant.id}`);
    };

    // 4. Update Quantity (Using Variant ID based route logic)
    const handleUpdateQuantity = (newQty: number) => {
        executeAction('patch', `/cart/items/${selectedVariant.id}/quantity`, {
            quantity: newQty,
        });
    };

    // 5. Wishlist Toggle
    const handleToggleWishlist = () => {
        executeAction('post', `/wishlist/toggle/${selectedVariant.id}`);
    };

    return (
        <div className="flex flex-col gap-6">
            <PriceSection
                price={selectedVariant.price}
                compareAtPrice={selectedVariant.compareAtPrice}
                discountPercent={selectedVariant.discountPercent}
            />

            {product.description && (
                <div
                    className="prose prose-sm dark:prose-invert max-w-none leading-relaxed text-muted-foreground"
                    dangerouslySetInnerHTML={{
                        __html: product.description,
                    }}
                />
            )}

            {/* Attributes Selection */}
            <div className="space-y-4">
                {product.attributes.map((attr) => (
                    <div
                        key={attr.id}
                        className="space-y-2"
                    >
                        <div className="flex items-center justify-between text-xs">
                            <span className="font-medium text-foreground">
                                {attr.name}:
                            </span>
                            <span className="text-muted-foreground">
                                {attr.selectedValue?.value}
                            </span>
                        </div>
                        <div className="flex flex-wrap gap-2">
                            {attr.values.map((value) => {
                                const isSelected =
                                    attr.selectedValue?.id === value.id;
                                const isDisabled = !value.enabled;
                                return (
                                    <button
                                        key={value.id}
                                        onClick={() =>
                                            !isDisabled &&
                                            !isSelected &&
                                            handleAttributeChange(
                                                attr.id,
                                                value.id,
                                            )
                                        }
                                        disabled={isDisabled}
                                        className={cn(
                                            'min-w-12 rounded-md border px-3 py-1.5 text-sm transition-all',
                                            isSelected
                                                ? 'border-primary bg-primary/10 font-semibold text-primary ring-1 ring-primary/20'
                                                : 'bg-background text-muted-foreground hover:bg-muted',
                                            isDisabled
                                                ? 'cursor-not-allowed border-dashed border-input bg-gray-50 text-gray-400 opacity-50'
                                                : 'border-input',
                                        )}
                                    >
                                        {value.value}
                                    </button>
                                );
                            })}
                        </div>
                    </div>
                ))}
            </div>

            {/* Quantity & Actions */}
            <div className="flex flex-col gap-3 sm:flex-row">
                {/* Quantity Control - Disabled if not in cart */}
                <div
                    className={cn(
                        'flex h-11 w-fit shrink-0 items-center rounded-md border bg-background transition-opacity',
                        !inCart && 'cursor-not-allowed bg-muted/30 opacity-60',
                    )}
                >
                    <button
                        // لا يمكن النقصان إذا لم يكن في السلة أو الكمية 1 أو جاري التحميل
                        disabled={!inCart || displayQuantity <= 1 || isLoading}
                        onClick={() =>
                            handleUpdateQuantity(displayQuantity - 1)
                        }
                        className="h-full px-3 transition-colors hover:bg-muted disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <Minus className="h-3 w-3" />
                    </button>
                    <div className="w-10 text-center text-sm font-medium">
                        {isLoading && inCart ? (
                            <Loader2 className="mx-auto h-3 w-3 animate-spin" />
                        ) : (
                            displayQuantity
                        )}
                    </div>
                    <button
                        // لا يمكن الزيادة إذا لم يكن في السلة أو نفد المخزون أو جاري التحميل
                        disabled={
                            !inCart ||
                            (isOutOfStock &&
                                displayQuantity >= selectedVariant.quantity) ||
                            isLoading
                        }
                        onClick={() =>
                            handleUpdateQuantity(displayQuantity + 1)
                        }
                        className="h-full px-3 transition-colors hover:bg-muted disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <Plus className="h-3 w-3" />
                    </button>
                </div>

                {/* Add To Cart OR Go To Cart */}
                {!inCart ? (
                    <Button
                        className="h-11 flex-1 text-sm font-bold shadow-sm"
                        disabled={isOutOfStock || isLoading}
                        onClick={handleAddToCart}
                    >
                        {isLoading ? (
                            <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                        ) : (
                            <ShoppingCart className="mr-2 h-4 w-4" />
                        )}
                        {isOutOfStock ? 'نفدت الكمية' : 'أضف للسلة'}
                    </Button>
                ) : (
                    <Button
                        variant="secondary"
                        className="h-11 flex-1 border border-primary/20 bg-primary/5 text-sm font-bold text-primary shadow-sm hover:bg-primary/10"
                        onClick={() => router.visit('/cart')}
                    >
                        <ArrowRight className="mr-2 h-4 w-4" />
                        الذهاب للسلة
                    </Button>
                )}

                {/* Wishlist Toggle */}
                <Button
                    variant="outline"
                    className={cn(
                        'h-11 w-11 shrink-0 p-0 transition-colors',
                        inWishlist &&
                            'border-red-200 bg-red-50 text-red-500 hover:bg-red-100',
                    )}
                    onClick={handleToggleWishlist}
                    disabled={isLoading}
                >
                    <Heart
                        className={cn(
                            'h-5 w-5 transition-all',
                            inWishlist && 'scale-110 fill-current',
                        )}
                    />
                </Button>
            </div>

            <StockStatusLine quantity={selectedVariant.quantity} />
        </div>
    );
}
