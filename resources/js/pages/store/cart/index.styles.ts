export const styles = {
    container: 'max-w-7xl mx-auto px-4 py-6 sm:py-8',
    emptyContainer:
        'flex flex-col items-center justify-center py-20 text-center bg-card border border-border rounded-2xl',
    emptyIconWrapper: 'relative mb-6',
    emptyIconBg:
        'absolute inset-0 bg-primary/10 rounded-full blur-xl scale-150',
    emptyIcon: 'relative h-20 w-20 text-primary/60',
    emptyTitle: 'text-2xl font-bold text-foreground mb-2',
    emptyText: 'text-muted-foreground mb-8 max-w-sm',
    shopButton: 'px-8 py-3 gap-2',
    content: 'grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8',
    itemsCard:
        'lg:col-span-2 bg-card border border-border rounded-2xl overflow-hidden',
    cardHeader:
        'flex flex-col items-start gap-3 sm:flex-row sm:items-center sm:justify-between gap-4 p-4 sm:p-5 border-b border-border bg-muted/30',
    cardHeaderStart: 'flex items-center gap-3 sm:gap-4',
    selectAllButton:
        'flex items-center justify-center cursor-pointer p-1 hover:opacity-80 transition-opacity',
    checkbox:
        'h-5 w-5 rounded border-2 border-primary/60 data-[state=checked]:bg-primary data-[state=checked]:border-primary transition-all',
    cardTitleGroup: 'flex flex-col',
    cardTitle:
        'text-lg sm:text-xl font-bold text-foreground flex items-center gap-2',
    cardTitleIcon: 'h-5 w-5 text-primary',
    cardSubtitle: 'text-xs sm:text-sm text-muted-foreground',
    selectedBadge:
        'text-xs sm:text-sm font-medium text-primary bg-primary/10 px-3 py-1 rounded-full self-end sm:self-auto',
    itemsList: 'divide-y divide-border',
    item: 'relative flex flex-wrap items-center gap-3 sm:gap-4 p-4 sm:p-5 pr-4 sm:pr-14 pb-16 sm:pb-5 transition-all duration-200 hover:bg-muted/30 sm:flex-nowrap',
    itemSelected: 'bg-primary/5',
    deleteButton:
        'absolute top-auto bottom-4 left-4 sm:top-3 sm:bottom-auto sm:left-3 p-1.5 text-muted-foreground/60 hover:text-destructive hover:bg-destructive/10 rounded-md transition-all duration-200',
    deleteIcon: 'h-4 w-4',
    itemCheckbox:
        'absolute top-3 right-3 translate-y-0 sm:top-1/2 sm:right-4 sm:-translate-y-1/2 cursor-pointer p-1.5 hover:bg-primary/10 rounded-md transition-all duration-200',
    itemImageLink: 'flex-shrink-0',
    itemImage:
        'w-20 h-20 sm:w-24 sm:h-24 object-cover rounded-xl border border-border/30 hover:border-primary/30 transition-all duration-300 shadow-sm',
    itemInfo: 'flex-1 min-w-0 flex flex-col gap-1.5',
    itemName:
        'text-sm sm:text-base font-semibold text-foreground hover:text-primary transition-colors line-clamp-2 leading-snug',
    itemAttributes: 'flex flex-wrap items-center gap-2',
    itemAttributeBadge:
        'gap-2 rounded-lg border-border/60 bg-background/70 text-[11px] font-semibold text-foreground',
    itemAttributeColor: 'h-2.5 w-2.5 rounded-full border border-border/60',
    itemPriceRow: 'flex items-center gap-2 flex-wrap',
    itemUnitPrice: 'text-sm sm:text-base font-bold text-primary',
    itemComparePrice: 'text-xs sm:text-sm text-muted-foreground line-through',
    itemTotalColumn:
        'absolute bottom-4 right-4 flex flex-row items-center gap-4 sm:relative sm:bottom-auto sm:right-auto sm:flex-col sm:items-end sm:gap-3 flex-shrink-0',
    itemTotal:
        'text-base sm:text-lg font-bold text-foreground whitespace-nowrap',
    quantityControl:
        'flex items-center bg-muted/60 rounded-lg border border-border/50',
    quantityButton:
        'h-8 w-8 sm:h-9 sm:w-9 hover:bg-background/80 rounded-md transition-colors',
    quantityIcon: 'h-3.5 w-3.5',
    quantityValue:
        'w-10 sm:w-12 text-center font-semibold text-foreground text-sm sm:text-base',
    summarySection: 'lg:col-span-1',
    summaryCard:
        'p-6 bg-card border border-border rounded-xl sticky top-4 shadow-sm',
    summaryTitle: 'text-xl font-bold text-foreground mb-4',
    summaryInfo:
        'flex items-center gap-2 text-sm text-muted-foreground mb-4 p-3 bg-muted/50 rounded-lg',
    summaryInfoIcon: 'h-4 w-4 text-primary',
    summaryDivider: 'h-px bg-border my-4',
    summaryRow: 'flex justify-between items-center py-2 text-sm',
    shippingText: 'text-muted-foreground',
    summaryTotal: 'text-lg font-bold text-foreground pt-2',
    totalPrice: 'text-primary text-xl',
    checkoutButton: 'w-full mt-6 gap-2',
    checkoutBadge:
        'inline-flex items-center justify-center h-5 min-w-5 px-1.5 text-xs font-bold bg-primary-foreground text-primary rounded-full',
    noSelectionWarning: 'text-center text-sm text-muted-foreground mt-3',
    notificationButton: '',
} as const;
