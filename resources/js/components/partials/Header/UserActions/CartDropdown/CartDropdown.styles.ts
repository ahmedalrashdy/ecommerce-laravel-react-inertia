export const styles = {
    container: 'relative',
    cartButton:
        'group hidden lg:flex relative items-center justify-center h-10 w-10 text-foreground hover:bg-primary/10 hover:text-primary rounded-xl transition-all duration-300 cursor-pointer',
    cartIcon:
        'h-4.5 w-4.5 transition-transform duration-300 group-hover:scale-110',
    cartBadge:
        'absolute -top-0.5 -right-0.5 h-5 w-5 flex items-center justify-center text-[10px] bg-gradient-to-br from-destructive to-badge-sale text-white rounded-full font-bold shadow-md animate-pulse',
    mobileLink:
        'lg:hidden relative flex items-center justify-center h-10 w-10 text-foreground hover:bg-primary/10 hover:text-primary rounded-xl transition-all duration-300',
    dropdown:
        'absolute left-0 top-full mt-2 w-80 bg-card/95 backdrop-blur-xl border border-border/50 rounded-xl shadow-2xl z-50 overflow-hidden animate-in fade-in slide-in-from-top-2 duration-200',
    dropdownHeader: 'p-4 border-b border-border/50',
    dropdownTitle: 'text-lg font-bold text-foreground',
    itemsCount: 'text-sm text-muted-foreground',
    dropdownContent: 'max-h-96 overflow-y-auto',
    loadingContainer:
        'flex flex-col items-center justify-center p-8 gap-2 text-muted-foreground',
    loader: 'h-6 w-6 animate-spin',
    emptyContainer:
        'flex flex-col items-center justify-center p-8 gap-2 text-muted-foreground',
    emptyIcon: 'h-12 w-12 opacity-50',
    emptyText: 'text-sm',
    itemsList: 'p-2 space-y-2',
    item: 'flex items-center gap-3 p-2 rounded-lg hover:bg-accent/50 transition-colors',
    itemImage: 'h-16 w-16 object-cover rounded-lg',
    itemInfo: 'flex-1 min-w-0',
    itemName: 'text-sm font-medium text-foreground truncate',
    itemPrice: 'text-xs text-muted-foreground',
    moreItems: 'text-xs text-center text-muted-foreground p-2',
    dropdownFooter: 'p-4 border-t border-border/50',
    viewCartButton:
        'w-full flex items-center justify-center px-4 py-2 bg-primary text-primary-foreground rounded-lg font-medium hover:bg-primary/90 transition-colors',
    removeButton:
        'flex-shrink-0 p-1.5 text-muted-foreground hover:text-destructive hover:bg-destructive/10 rounded-lg transition-colors',
    removeIcon: 'h-4 w-4',
    subtotalContainer:
        'flex items-center justify-between px-4 py-3 bg-muted/50',
    subtotalLabel: 'text-sm text-muted-foreground',
    subtotalValue: 'text-base font-bold text-foreground',
    itemImageSkeleton: 'h-16 w-16 rounded-lg flex-shrink-0',
} as const;
