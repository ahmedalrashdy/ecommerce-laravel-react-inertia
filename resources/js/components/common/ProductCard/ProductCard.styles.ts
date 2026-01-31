export const styles = {
    gridCard:
        'group h-full flex flex-col bg-card rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden border border-border/60',
    gridImageContainer:
        'relative overflow-hidden bg-muted/20 aspect-[4/5] md:aspect-square',
    gridImage: 'w-full h-full object-cover transition-transform duration-500',
    gridBadge:
        'absolute top-3 right-3 text-white text-[10px] md:text-xs font-bold px-2.5 py-1 rounded-full shadow-md z-10',
    gridQuickActions:
        'absolute top-3 left-3 hidden md:flex flex-col gap-2 opacity-0 translate-x-2 transition-all duration-300 z-20 pointer-events-auto group-hover:opacity-100 group-hover:translate-x-0',
    gridQuickActionButton:
        'rounded-full shadow-lg h-9 w-9 bg-white/95 dark:bg-zinc-800/95 backdrop-blur-sm border border-transparent dark:border-zinc-700/50 hover:bg-white dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-200 hover:text-zinc-900 dark:hover:text-white transition-colors',
    gridQuickActionIcon:
        'h-4 w-4 transition-colors text-zinc-600 dark:text-zinc-300',
    gridQuickActionIconActive: 'fill-red-500 text-red-500',
    gridQuickViewLink:
        'flex justify-center items-center rounded-full shadow-lg h-9 w-9 bg-white/95 dark:bg-zinc-800/95 backdrop-blur-sm border border-transparent dark:border-zinc-700/50 hover:bg-white dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-200 hover:text-zinc-900 dark:hover:text-white transition-colors',
    gridQuickViewIcon: 'h-4 w-4 text-zinc-600 dark:text-zinc-300',
    gridAddToCartOverlay:
        'absolute bottom-3 left-3 right-3 translate-y-full opacity-0 transition-all duration-300 hidden md:block z-20 pointer-events-auto group-hover:translate-y-0 group-hover:opacity-100',
    gridAddToCartButton: 'w-full gap-2 shadow-lg rounded-xl font-semibold',
    gridAddToCartIcon: 'h-4 w-4',
    gridMobileWishlistButton:
        'absolute top-2 left-2 p-2 rounded-full bg-black/20 dark:bg-white/20 backdrop-blur-sm md:hidden z-20 pointer-events-auto',
    gridMobileWishlistIcon:
        'h-4 w-4 text-white dark:text-zinc-100 drop-shadow-md',
    gridMobileWishlistIconActive: 'fill-red-500 text-red-500',
    gridProductInfo: 'p-3 md:p-4 flex flex-col flex-1',
    gridProductName:
        'text-sm md:text-base font-bold text-foreground mb-1.5 leading-snug line-clamp-1 transition-colors',
    gridDescription:
        'text-[11px] md:text-sm text-muted-foreground mb-3 line-clamp-2 leading-relaxed',
    gridSpacer: 'mt-auto',
    gridRating: 'flex items-center gap-1.5 mb-2',
    gridRatingStar: 'h-3.5 w-3.5 fill-amber-400 text-amber-400',
    gridRatingValue: 'text-xs md:text-sm font-semibold',
    gridRatingCount: 'text-[10px] md:text-xs text-muted-foreground',
    gridPriceContainer: 'flex items-center justify-between gap-2',
    gridPriceWrapper:
        'flex flex-col md:flex-row md:items-baseline gap-0.5 md:gap-2',
    gridPrice: 'text-sm md:text-lg font-bold text-primary',
    gridComparePrice:
        'text-[10px] md:text-sm text-muted-foreground line-through decoration-red-400/50',
    gridMobileAddButton: 'h-8 w-8 rounded-full md:hidden shrink-0',
    gridMobileAddIcon: 'h-4 w-4',
    notificationButton: '',
    listCard:
        'group relative flex flex-row bg-card rounded-2xl border border-border/60 hover:shadow-lg hover:border-primary/30 transition-all duration-300 md:min-h-52 lg:min-h-60',
    listImageContainer:
        'relative w-32 md:w-52 lg:w-64 shrink-0 overflow-hidden bg-muted',
    listImage: 'w-full h-full object-cover transition-transform duration-500',
    listBadge:
        'absolute top-2 right-2 text-[10px] md:text-xs font-bold px-2 py-0.5 md:py-1 rounded-full shadow-sm z-10 text-white',
    listDiscountBadge:
        'absolute top-2 right-2 bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm z-10',
    listContent: 'flex-1 flex flex-col p-3 md:p-5 justify-between',
    listHeader: 'flex justify-between items-start mb-1 md:mb-2',
    listProductName:
        'text-sm md:text-lg font-bold text-foreground line-clamp-1 transition-colors',
    listWishlistButton:
        'h-8 w-8 -mr-2 -mt-2 text-zinc-500 dark:text-zinc-400 hover:text-red-500 dark:hover:text-red-400 hover:bg-transparent transition-colors',
    listWishlistIcon: 'h-4 w-4 md:h-5 md:w-5',
    listWishlistIconActive: 'fill-red-500 text-red-500',
    listRating: 'flex items-center gap-1 mb-2',
    listRatingStar:
        'h-3 w-3 md:h-4 md:w-4 fill-current text-amber-400',
    listRatingValue: 'text-xs md:text-sm font-medium pt-0.5',
    listRatingCount:
        'text-[10px] md:text-xs text-muted-foreground pt-0.5',
    listDescription:
        'hidden md:block text-sm text-muted-foreground line-clamp-2 leading-relaxed',
    listFooter: 'flex items-end justify-between mt-2',
    listPriceWrapper: 'flex flex-col',
    listComparePrice:
        'text-[10px] md:text-xs text-muted-foreground line-through decoration-red-400/50 mb-0.5',
    listPrice: 'text-base md:text-xl font-bold text-primary',
    listPriceCurrency: 'text-xs md:text-sm font-normal text-muted-foreground',
    listActions: 'flex items-center gap-2',
    listMobileAddButton: 'md:hidden h-9 w-9 rounded-full shadow-sm',
    listMobileAddIcon: 'h-4 w-4',
    listDesktopAddButton: 'hidden md:flex gap-2 shadow-sm',
    listDesktopAddIcon: 'h-4 w-4',
    listQuickViewButton:
        'hidden md:flex h-9 w-9 text-zinc-600 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-white border-zinc-200 dark:border-zinc-700 hover:border-zinc-300 dark:hover:border-zinc-600 transition-colors',
    listQuickViewIcon: 'h-4 w-4',
} as const;
