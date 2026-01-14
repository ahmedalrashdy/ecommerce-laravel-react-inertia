import { index as productsIndex } from '@/routes/store/products';
import { router } from '@inertiajs/react';
import debounce from 'lodash/debounce';
import { useCallback, useEffect, useMemo, useState } from 'react';

interface ShopFiltersState {
    sort: string;
    minPrice: number;
    maxPrice: number;
    categories: string[];
    brands: string[];
    search: string | null;
}

interface Filters {
    sort: string;
    minPrice: number | null;
    maxPrice: number | null;
    categories: string[];
    brands: string[];
    search: string | null;
    currentCategory: string | null;
    currentBrand: string | null;
}

interface UseShopFiltersProps {
    initialFilters: Filters;
    serverMaxPrice: number;
    currentCategorySlug?: string | null;
    currentBrandSlug?: string | null;
}

export function useShopFilters({
    initialFilters,
    serverMaxPrice,
    currentCategorySlug,
    currentBrandSlug,
}: UseShopFiltersProps) {
    const maxPriceLimit = serverMaxPrice || 5000;

    const [localFilters, setLocalFilters] = useState<ShopFiltersState>({
        sort: initialFilters.sort ?? 'new-arrivals',
        minPrice: Number(initialFilters.minPrice ?? 0),
        maxPrice: Number(initialFilters.maxPrice ?? maxPriceLimit),
        categories: initialFilters.categories ?? [],
        brands: initialFilters.brands ?? [],
        search: initialFilters.search ?? null,
    });

    const [viewMode, setViewMode] = useState<'grid' | 'list'>('grid');
    const [isFilterOpen, setIsFilterOpen] = useState(false);

    const buildQueryParams = useCallback(
        (state: ShopFiltersState, options?: { includeSearch?: boolean }) => {
            const params: Record<string, string | number> = {};
            const includeSearch = options?.includeSearch ?? true;

            const search = includeSearch ? (state.search ?? '').trim() : '';
            if (search.length >= 2) params.q = search;

            if (state.sort !== 'new-arrivals') params.sort = state.sort;

            if (state.minPrice > 0)
                params['filter[min_price]'] = state.minPrice;
            if (state.maxPrice < maxPriceLimit)
                params['filter[max_price]'] = state.maxPrice;

            const allCategories = [...state.categories];
            if (
                currentCategorySlug &&
                !allCategories.includes(currentCategorySlug)
            ) {
                allCategories.push(currentCategorySlug);
            }
            if (allCategories.length)
                params['filter[categories]'] = allCategories.join(',');

            const allBrands = [...state.brands];
            if (currentBrandSlug && !allBrands.includes(currentBrandSlug)) {
                allBrands.push(currentBrandSlug);
            }
            if (allBrands.length)
                params['filter[brands]'] = allBrands.join(',');

            return params;
        },
        [maxPriceLimit, currentCategorySlug, currentBrandSlug],
    );

    const shouldRedirectToGeneral = useCallback(
        (state: ShopFiltersState) => {
            const hasExtraCategories =
                !!currentCategorySlug &&
                state.categories.some((s) => s !== currentCategorySlug);

            const hasExtraBrands =
                !!currentBrandSlug &&
                state.brands.some((s) => s !== currentBrandSlug);

            return hasExtraCategories || hasExtraBrands;
        },
        [currentCategorySlug, currentBrandSlug],
    );

    const applyNow = useCallback(
        (
            state: ShopFiltersState,
            opts?: { page?: number; includeSearch?: boolean },
        ) => {
            const redirect = shouldRedirectToGeneral(state);
            const url = redirect
                ? productsIndex.url()
                : window.location.pathname;

            const params = buildQueryParams(state, {
                includeSearch: opts?.includeSearch ?? true,
            });
            if (opts?.page && opts.page > 1) params.page = opts.page;

            router.get(url, params, {
                replace: true,
                preserveScroll: !redirect,
                preserveState: !redirect,
                ...(redirect
                    ? {}
                    : { only: ['products', 'filters', 'maxPrice'] }),
            });
        },
        [buildQueryParams, shouldRedirectToGeneral],
    );

    const applyDebounced = useMemo(
        () => debounce((s: ShopFiltersState) => applyNow(s), 450),
        [applyNow],
    );

    // ✅ تنظيف timers
    useEffect(() => {
        return () => applyDebounced.cancel();
    }, [applyDebounced]);

    const updateFilter = useCallback(
        (
            key: keyof ShopFiltersState,
            value: ShopFiltersState[keyof ShopFiltersState],
        ) => {
            setLocalFilters((prev) => {
                const next = { ...prev, [key]: value } as ShopFiltersState;
                applyDebounced(next);
                return next;
            });
        },
        [applyDebounced],
    );

    const handlePriceChange = useCallback(
        (range: [number, number]) => {
            setLocalFilters((prev) => {
                const next = {
                    ...prev,
                    minPrice: range[0],
                    maxPrice: range[1],
                };
                applyDebounced(next);
                return next;
            });
        },
        [applyDebounced],
    );

    const clearAllFilters = useCallback(() => {
        applyDebounced.cancel();

        const reset: ShopFiltersState = {
            sort: 'new-arrivals',
            minPrice: 0,
            maxPrice: maxPriceLimit,
            categories: [],
            brands: [],
            search: null,
        };

        setLocalFilters(reset);

        router.get(
            productsIndex.url(),
            {},
            { preserveState: false, preserveScroll: false, replace: true },
        );
    }, [applyDebounced, maxPriceLimit]);

    const clearSearch = useCallback(() => {
        applyDebounced.cancel();

        setLocalFilters((prev) => {
            const next = { ...prev, search: null };
            applyNow(next, { includeSearch: false });
            return next;
        });
    }, [applyDebounced, applyNow]);

    const handlePageChange = useCallback(
        (page: number) => {
            // ✅ منع الطلب المؤجل من “الرجوع” بعد pagination
            applyDebounced.cancel();
            applyNow(localFilters, { page });
        },
        [applyDebounced, applyNow, localFilters],
    );

    const activeFiltersCount =
        localFilters.categories.length +
        localFilters.brands.length +
        (localFilters.minPrice > 0 || localFilters.maxPrice < maxPriceLimit
            ? 1
            : 0) +
        (localFilters.search && localFilters.search.trim().length >= 2
            ? 1
            : 0) +
        (currentCategorySlug ? 1 : 0) +
        (currentBrandSlug ? 1 : 0);

    return {
        localFilters,
        viewMode,
        setViewMode,
        isFilterOpen,
        setIsFilterOpen,
        updateFilter,
        handlePriceChange,
        clearAllFilters,
        clearSearch,
        handlePageChange,
        activeFiltersCount,
    };
}

import {
    ActiveFilters,
    Pagination,
    ProductsGrid,
    ShopToolbar,
    TrustFeatures,
} from '@/features/products';
import { FilterSidebar } from '@/features/products/FilterSidebar/FilterSidebar';
import StoreLayout from '@/layouts/StoreLayout';
import { PaginatedResponse } from '@/types';
import { Head, usePage } from '@inertiajs/react';
interface Filters {
    sort: string;
    minPrice: number | null;
    maxPrice: number | null;
    categories: string[];
    brands: string[];
    search: string | null;
    currentCategory: string | null;
    currentBrand: string | null;
}

interface PageProps {
    [key: string]: unknown;
    products: PaginatedResponse<App.Data.Basic.ProductData>;
    mainCategories: App.Data.Basic.CategoryData[];
    featuredBrands: App.Data.Basic.BrandData[];
    currentCategory: App.Data.Basic.CategoryData | null;
    currentBrand: App.Data.Basic.BrandData | null;
    filters: Filters;
    maxPrice: number;
}

export default function Shop() {
    const {
        products,
        mainCategories,
        featuredBrands,
        currentCategory,
        currentBrand,
        filters: serverFilters,
        maxPrice: serverMaxPrice,
    } = usePage<PageProps>().props;

    const maxPriceLimit = serverMaxPrice || 5000;
    const {
        localFilters,
        viewMode,
        setViewMode,
        isFilterOpen,
        setIsFilterOpen,
        updateFilter,
        handlePriceChange,
        clearAllFilters,
        clearSearch,
        handlePageChange,
        activeFiltersCount,
    } = useShopFilters({
        initialFilters: serverFilters,
        serverMaxPrice: maxPriceLimit,
        currentCategorySlug: currentCategory?.slug,
        currentBrandSlug: currentBrand?.slug,
    });

    const getPageTitle = () => {
        if (currentCategory) return `${currentCategory.name}`;
        if (currentBrand) return `${currentBrand.name}`;
        return 'تصفح جميع المنتجات';
    };

    return (
        <StoreLayout>
            <Head title={getPageTitle()} />
            <TrustFeatures />

            <div className="container mx-auto px-4 py-12">
                <div className="flex items-start gap-8">
                    {/* Sidebar */}
                    <aside className="hidden w-72 flex-shrink-0 lg:block">
                        <div className="rounded-xl border border-border bg-card p-5 shadow-sm">
                            <FilterSidebar
                                categories={mainCategories}
                                brands={featuredBrands}
                                priceRange={[
                                    localFilters.minPrice,
                                    localFilters.maxPrice,
                                ]} // Use local state
                                maxPrice={maxPriceLimit}
                                onPriceChange={handlePriceChange}
                                selectedCategories={localFilters.categories}
                                onCategoryChange={(slugs) =>
                                    updateFilter('categories', slugs)
                                }
                                selectedBrands={localFilters.brands}
                                onBrandChange={(slugs) =>
                                    updateFilter('brands', slugs)
                                }
                                onClearAll={clearAllFilters}
                                currentCategorySlug={currentCategory?.slug}
                                currentBrandSlug={currentBrand?.slug}
                            />
                        </div>
                    </aside>

                    {/* Main Content */}
                    <div className="min-w-0 flex-1">
                        <ShopToolbar
                            viewMode={viewMode}
                            onViewModeChange={setViewMode}
                            sortBy={localFilters.sort}
                            onSortChange={(sort) => updateFilter('sort', sort)}
                            isFilterOpen={isFilterOpen}
                            onFilterOpenChange={setIsFilterOpen}
                            activeFiltersCount={activeFiltersCount}
                            mainCategories={mainCategories}
                            featuredBrands={featuredBrands}
                            priceRange={[
                                localFilters.minPrice,
                                localFilters.maxPrice,
                            ]}
                            maxPrice={maxPriceLimit}
                            onPriceChange={handlePriceChange}
                            selectedCategories={localFilters.categories}
                            onCategoryChange={(slugs) =>
                                updateFilter('categories', slugs)
                            }
                            selectedBrands={localFilters.brands}
                            onBrandChange={(slugs) =>
                                updateFilter('brands', slugs)
                            }
                            onClearAll={clearAllFilters}
                            productsCount={products.total}
                            currentCategorySlug={currentCategory?.slug}
                            currentBrandSlug={currentBrand?.slug}
                        />

                        <ActiveFilters
                            selectedCategories={localFilters.categories}
                            onCategoryClear={() =>
                                updateFilter('categories', [])
                            }
                            selectedBrands={localFilters.brands}
                            onBrandClear={() => updateFilter('brands', [])}
                            searchQuery={localFilters.search}
                            onSearchClear={clearSearch}
                            onClearAll={clearAllFilters}
                            priceRange={[
                                localFilters.minPrice,
                                localFilters.maxPrice,
                            ]}
                            maxPrice={maxPriceLimit}
                            currentCategorySlug={currentCategory?.slug}
                            currentBrandSlug={currentBrand?.slug}
                            categories={mainCategories}
                            brands={featuredBrands}
                        />

                        <ProductsGrid
                            products={products.data}
                            viewMode={viewMode}
                        />

                        <Pagination
                            currentPage={products.current_page}
                            totalPages={products.last_page}
                            totalProducts={products.total}
                            productsPerPage={products.per_page}
                            onPageChange={handlePageChange}
                            links={products.links}
                        />
                    </div>
                </div>
            </div>
        </StoreLayout>
    );
}
