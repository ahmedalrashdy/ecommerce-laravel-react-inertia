import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import { ChevronLeft, ChevronRight } from 'lucide-react';
interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}
interface PaginationProps {
    currentPage: number;
    totalPages: number;
    totalProducts: number;
    productsPerPage: number;
    onPageChange: (page: number) => void;
    links?: PaginationLink[];
}

export function Pagination({
    currentPage,
    totalPages,
    totalProducts,
    productsPerPage,
    onPageChange,
    links = [],
}: PaginationProps) {
    const startProduct = (currentPage - 1) * productsPerPage + 1;
    const endProduct = Math.min(currentPage * productsPerPage, totalProducts);

    if (totalPages <= 1) {
        return null;
    }

    const getVisiblePages = () => {
        const pageLinks = links.filter((link) => {
            const label = link.label;
            return !label.includes('&laquo;') && !label.includes('&raquo;');
        });

        if (pageLinks.length > 0) {
            return pageLinks
                .map((link) => ({
                    page: parseInt(link.label, 10),
                    active: link.active,
                    isEllipsis: link.label === '...',
                }))
                .filter((item) => !isNaN(item.page) || item.isEllipsis);
        }

        const pages: { page: number; active: boolean; isEllipsis?: boolean }[] =
            [];
        const delta = 2;

        for (let i = 1; i <= totalPages; i++) {
            if (
                i === 1 ||
                i === totalPages ||
                (i >= currentPage - delta && i <= currentPage + delta)
            ) {
                pages.push({ page: i, active: i === currentPage });
            } else if (
                pages.length > 0 &&
                !pages[pages.length - 1].isEllipsis
            ) {
                pages.push({ page: 0, active: false, isEllipsis: true });
            }
        }

        return pages;
    };

    const visiblePages = getVisiblePages();

    return (
        <div className="mt-12 flex flex-col items-center justify-center gap-4">
            <div className="flex items-center gap-2 rounded-lg border border-border bg-card p-1 shadow-sm">
                <Button
                    variant="ghost"
                    size="icon"
                    disabled={currentPage === 1}
                    onClick={() => onPageChange(currentPage - 1)}
                    className="h-9 w-9"
                >
                    <ChevronRight className="h-4 w-4" />
                </Button>

                <div className="flex items-center gap-1 px-2">
                    {visiblePages.map((item, index) =>
                        item.isEllipsis ? (
                            <span
                                key={`ellipsis-${index}`}
                                className="px-1 text-muted-foreground"
                            >
                                ...
                            </span>
                        ) : (
                            <button
                                key={item.page}
                                onClick={() => onPageChange(item.page)}
                                className={cn(
                                    'h-9 w-9 rounded-md text-sm font-medium transition-all',
                                    item.active
                                        ? 'bg-primary text-primary-foreground shadow-md'
                                        : 'text-muted-foreground hover:bg-muted hover:text-foreground',
                                )}
                            >
                                {item.page}
                            </button>
                        ),
                    )}
                </div>

                <Button
                    variant="ghost"
                    size="icon"
                    disabled={currentPage === totalPages}
                    onClick={() => onPageChange(currentPage + 1)}
                    className="h-9 w-9"
                >
                    <ChevronLeft className="h-4 w-4" />
                </Button>
            </div>
            <p className="text-sm text-muted-foreground">
                عرض المنتجات {startProduct}-{endProduct} من أصل {totalProducts}
            </p>
        </div>
    );
}
