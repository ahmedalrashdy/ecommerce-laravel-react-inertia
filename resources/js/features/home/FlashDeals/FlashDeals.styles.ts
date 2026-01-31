export const styles = {
    section:
        'bg-gradient-to-br from-badge-sale/10 via-accent/10 to-warning/10 py-12 md:py-16 dark:from-badge-sale/5 dark:via-accent/5 dark:to-warning/5',
    container: 'container mx-auto px-4',
    header:
        'mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between',
    headerContent: 'mb-3 flex items-center gap-3',
    iconContainer: 'rounded-lg bg-badge-sale p-2 shadow-lg',
    icon: 'h-6 w-6 text-white',
    title: 'text-3xl font-bold text-foreground md:text-4xl',
    subtitle: 'text-muted-foreground',
    timerContainer:
        'flex items-center gap-2 rounded-xl border-2 border-badge-sale/20 bg-card p-4 shadow-lg dark:border-badge-sale/30',
    timerIcon: 'h-5 w-5 text-badge-sale',
    timerLabel: 'ml-2 text-sm text-muted-foreground',
    timerBlocks: 'flex gap-2',
    timerSeparator: 'text-2xl font-bold text-badge-sale',
    timeBlock: 'flex flex-col items-center',
    timeBlockValue:
        'min-w-[50px] rounded-lg bg-badge-sale px-3 py-2 text-center text-xl font-bold text-white shadow-md md:text-2xl',
    timeBlockLabel: 'mt-1 text-xs text-muted-foreground',
    productsContainer: 'relative',
    productsScroll:
        'scrollbar-hide flex snap-x snap-mandatory gap-4 overflow-x-auto pb-4',
    viewAllContainer: 'mt-8 text-center',
    viewAllButton: 'gap-2 transition-smooth hover:scale-105',
    viewAllIcon: 'h-5 w-5',
    card: 'group w-[280px] shrink-0 snap-start overflow-hidden rounded-xl border border-border bg-card shadow-md transition-smooth hover:shadow-xl',
    imageContainer: 'relative overflow-hidden bg-muted/20',
    image:
        'h-56 w-full object-cover transition-transform duration-300 group-hover:scale-110',
    discountBadge:
        'absolute top-3 right-3 rounded-full bg-badge-sale px-3 py-1 text-sm font-bold text-white shadow-lg',
    quickActions:
        'absolute top-3 left-3 flex flex-col gap-2 opacity-0 transition-opacity group-hover:opacity-100',
    quickActionButton: 'rounded-full shadow-lg',
    quickActionIcon: 'h-4 w-4',
    productInfo: 'p-4',
    productName:
        'mb-2 line-clamp-2 text-sm font-semibold text-foreground transition-colors hover:text-primary',
    priceContainer: 'mb-3 flex items-center gap-2',
    price: 'text-xl font-bold text-destructive',
    originalPrice: 'text-sm text-muted-foreground line-through',
    stockContainer: 'mb-3',
    stockInfo: 'mb-1 flex justify-between text-xs text-muted-foreground',
    progressBar: 'h-2 overflow-hidden rounded-full bg-muted',
    progressBarFill: 'h-full rounded-full transition-all',
    progressBarHigh: 'bg-destructive',
    progressBarMedium: 'bg-warning',
    progressBarLow: 'bg-success',
    addToCartButton: 'w-full gap-2 transition-smooth hover:scale-105',
    addToCartIcon: 'h-4 w-4',
} as const;
