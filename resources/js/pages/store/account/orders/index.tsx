import {
    OrderCard,
    OrdersEmptyState,
    OrdersTabs,
    ReturnCard,
} from '@/features/account/orders';
import type { OrderSummary } from '@/features/account/orders/order-card';
import type { ReturnSummary } from '@/features/account/orders/return-card';
import type {
    OrdersTab,
    OrdersTabKey,
} from '@/features/account/orders/order-tabs';
import ordersRoutes from '@/routes/store/account/orders';
import productsRoutes from '@/routes/store/products';
import { Head, router, usePage } from '@inertiajs/react';
import { History, Package, RotateCcw, Search } from 'lucide-react';
import AccountLayout from '@/features/account/layout/AccountLayout';
import { Input } from '@/components/ui/input';
import type { ReactNode } from 'react';
import { useMemo, useState } from 'react';
import { useDebounce } from 'use-debounce';

type OrdersPageProps = {
    tab: OrdersTabKey;
    counts: {
        active: number;
        history: number;
        returns: number;
    };
    orders: OrderSummary[];
    returns: ReturnSummary[];
};

function OrdersPage() {
    const { tab, counts, orders, returns } = usePage<OrdersPageProps>().props;
    const [searchQuery, setSearchQuery] = useState('');
    const [dateFrom, setDateFrom] = useState('');
    const [dateTo, setDateTo] = useState('');
    const [debouncedSearchQuery] = useDebounce(searchQuery, 300);

    const tabs: OrdersTab[] = [
        {
            key: 'active',
            label: 'الطلبات الحالية',
            count: counts.active,
        },
        {
            key: 'history',
            label: 'الطلبات السابقة',
            count: counts.history,
        },
        {
            key: 'returns',
            label: 'المرتجعات',
            count: counts.returns,
        },
    ];

    const emptyState =
        tab === 'returns'
            ? {
                  title: 'لا توجد مرتجعات بعد',
                  description:
                      'عند إنشاء طلب إرجاع سيظهر هنا مع جميع حالاته وتحديثاته.',
                  icon: RotateCcw,
              }
            : tab === 'history'
              ? {
                    title: 'لا توجد طلبات سابقة',
                    description:
                        'عند اكتمال أي طلب سيظهر هنا للحفاظ على تاريخ مشترياتك.',
                    icon: History,
                    actionLabel: 'تصفح المنتجات',
                    actionHref: productsRoutes.index().url,
                }
              : {
                    title: 'لا توجد طلبات حالية',
                    description:
                        'ابدأ التسوق الآن وسيتم عرض طلباتك النشطة في هذه الصفحة.',
                    icon: Package,
                    actionLabel: 'تصفح المنتجات',
                    actionHref: productsRoutes.index().url,
                };

    const filteredOrders = useMemo(() => {
        if (tab === 'returns') {
            return orders;
        }

        const query = debouncedSearchQuery.trim().toLowerCase();
        const from = dateFrom || null;
        const to = dateTo || null;

        return orders.filter((order) => {
            const haystack = order.searchText ?? '';

            if (query && !haystack.includes(query)) {
                return false;
            }

            if (from && order.createdAtIso < from) {
                return false;
            }

            if (to && order.createdAtIso > to) {
                return false;
            }

            return true;
        });
    }, [orders, tab, debouncedSearchQuery, dateFrom, dateTo]);

    const showFilters = tab !== 'returns';
    const hasActiveFilters =
        debouncedSearchQuery.trim().length > 0 ||
        Boolean(dateFrom) ||
        Boolean(dateTo);
    const noResultsState = {
        title: 'لا توجد نتائج',
        description: 'جرّب تعديل كلمات البحث أو نطاق التاريخ.',
        icon: Package,
    };

    return (
        <>
            <Head title="طلباتي" />
            <div className="flex flex-col gap-6">
                <section className="rounded-3xl border border-border/60 bg-card p-6 shadow-sm">
                    <div className="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                        <div className="space-y-2">
                            <p className="text-xs text-muted-foreground">
                                طلباتي
                            </p>
                            <h1 className="text-3xl font-semibold">طلباتي</h1>
                            <p className="max-w-2xl text-sm text-muted-foreground">
                                تابع حالة الطلبات والمرتجعات من مكان واحد.
                            </p>
                        </div>
                        <div className="grid gap-3 sm:grid-cols-3">
                            <div className="rounded-2xl border border-border/60 bg-muted/40 px-4 py-3 text-center">
                                <p className="text-xs text-muted-foreground">
                                    الطلبات الحالية
                                </p>
                                <p className="text-lg font-semibold">
                                    {counts.active}
                                </p>
                            </div>
                            <div className="rounded-2xl border border-border/60 bg-muted/40 px-4 py-3 text-center">
                                <p className="text-xs text-muted-foreground">
                                    الطلبات السابقة
                                </p>
                                <p className="text-lg font-semibold">
                                    {counts.history}
                                </p>
                            </div>
                            <div className="rounded-2xl border border-border/60 bg-muted/40 px-4 py-3 text-center">
                                <p className="text-xs text-muted-foreground">
                                    المرتجعات
                                </p>
                                <p className="text-lg font-semibold">
                                    {counts.returns}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div className="mt-6">
                        <OrdersTabs
                            tabs={tabs}
                            activeTab={tab}
                            onTabChange={(nextTab) =>
                                router.visit(
                                    ordersRoutes.index.url({
                                        query: { tab: nextTab },
                                    }),
                                    {
                                        preserveScroll: true,
                                        replace: true,
                                    },
                                )
                            }
                        />
                    </div>
                    {showFilters && (
                        <div className="mt-6 flex flex-col gap-4 rounded-2xl border border-border/60 bg-muted/30 p-4 lg:flex-row lg:items-center">
                            <div className="relative flex-1">
                                <Search className="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                <Input
                                    value={searchQuery}
                                    onChange={(event) =>
                                        setSearchQuery(event.target.value)
                                    }
                                    placeholder="ابحث باسم المنتج أو الفئة أو العلامة التجارية"
                                    className="pr-9"
                                />
                            </div>
                            <div className="flex flex-col gap-3 sm:flex-row">
                                <div className="grid gap-1">
                                    <span className="text-xs text-muted-foreground">
                                        من
                                    </span>
                                    <Input
                                        type="date"
                                        value={dateFrom}
                                        onChange={(event) =>
                                            setDateFrom(event.target.value)
                                        }
                                        className="min-w-[160px]"
                                    />
                                </div>
                                <div className="grid gap-1">
                                    <span className="text-xs text-muted-foreground">
                                        إلى
                                    </span>
                                    <Input
                                        type="date"
                                        value={dateTo}
                                        onChange={(event) =>
                                            setDateTo(event.target.value)
                                        }
                                        className="min-w-[160px]"
                                    />
                                </div>
                            </div>
                        </div>
                    )}
                </section>

                {tab === 'returns' ? (
                    returns.length > 0 ? (
                        <div className="grid gap-5">
                            {returns.map((returnOrder) => (
                                <ReturnCard
                                    key={returnOrder.returnNumber}
                                    returnOrder={returnOrder}
                                />
                            ))}
                        </div>
                    ) : (
                        <OrdersEmptyState {...emptyState} />
                    )
                ) : filteredOrders.length > 0 ? (
                    <div className="grid gap-5">
                        {filteredOrders.map((order) => (
                            <OrderCard
                                key={order.orderNumber}
                                order={order}
                            />
                        ))}
                    </div>
                ) : hasActiveFilters ? (
                    <OrdersEmptyState {...noResultsState} />
                ) : (
                    <OrdersEmptyState {...emptyState} />
                )}
            </div>
        </>
    );
}

OrdersPage.layout = (page: ReactNode) => <AccountLayout>{page}</AccountLayout>;

export default OrdersPage;
