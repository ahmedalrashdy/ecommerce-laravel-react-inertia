export const styles = {
    container: 'max-w-7xl mx-auto px-4 py-8',
    header: 'mb-8',
    title: 'text-3xl font-bold text-foreground mb-2',
    subtitle: 'text-muted-foreground',
    emptyContainer:
        'flex flex-col items-center justify-center py-16 text-center',
    emptyIcon: 'h-24 w-24 text-muted-foreground mb-4 opacity-50',
    emptyTitle: 'text-2xl font-bold text-foreground mb-2',
    emptyText: 'text-muted-foreground mb-6',
    shopButton: 'px-6 py-3',
    itemsGrid:
        'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6',
    itemCard:
        'bg-card border border-border rounded-lg overflow-hidden hover:shadow-lg transition-shadow',
    itemImageContainer: 'relative aspect-square overflow-hidden',
    itemImage: 'w-full h-full object-cover',
    removeButton:
        'absolute top-2 right-2 bg-background/80 backdrop-blur-sm text-destructive hover:text-destructive hover:bg-background/90',
    removeIcon: 'h-4 w-4',
    itemInfo: 'p-4 space-y-2',
    itemName:
        'text-lg font-semibold text-foreground hover:text-primary transition-colors line-clamp-2',
    itemPrice: 'flex items-center gap-2',
    comparePrice: 'text-sm text-muted-foreground line-through',
    addToCartButton: 'w-full mt-2',
    addToCartIcon: 'h-4 w-4 mr-2',
} as const;
