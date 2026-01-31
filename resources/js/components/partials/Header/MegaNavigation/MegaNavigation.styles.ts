export const styles = {
    wrapper:
        'hidden lg:block border-b border-border/30 bg-gradient-to-l from-card/80 via-background to-card/80 dark:from-muted/10 dark:via-background dark:to-muted/10 shadow-sm backdrop-blur-sm',
    container: 'container mx-auto px-4',
    nav: 'flex items-center gap-1 h-12',
    allCategoriesLink:
        'group flex items-center gap-2 px-4 py-2.5 ml-3 bg-gradient-to-l from-primary to-primary/90 text-primary-foreground rounded-lg transition-all duration-300 font-semibold shadow-md hover:from-primary/90 hover:to-primary/80 hover:shadow-lg hover:scale-[1.02]',
    allCategoriesIcon:
        'h-4 w-4 transition-transform duration-300 group-hover:rotate-90',
    allCategoriesText: 'text-sm',
    allCategoriesChevron:
        'h-3.5 w-3.5 transition-transform duration-300 group-hover:rotate-180',
    categoryItem: 'relative',
    categoryLink:
        'flex items-center gap-1.5 px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 relative text-foreground/80 hover:text-primary hover:bg-muted/50',
    categoryLinkActive: 'text-primary bg-primary/5 dark:bg-primary/10',
    categoryChevron: 'h-3.5 w-3.5 transition-transform duration-200',
    categoryChevronActive: 'rotate-180',
    categoryIndicator:
        'absolute bottom-0 left-0 right-0 h-0.5 bg-primary rounded-full',
    megaPanel:
        'absolute left-0 right-0 bg-card/95 dark:bg-card/98 backdrop-blur-sm border-t border-b border-border shadow-2xl z-50 animate-in fade-in slide-in-from-top-2 duration-300',
    megaPanelContainer: 'container mx-auto px-4 py-6',
    megaPanelGrid: 'grid grid-cols-12 gap-6',
    subCategoriesColumn: 'col-span-3 border-l border-border pl-6',
    subCategoriesHeader: 'flex items-center gap-2 mb-4',
    subCategoriesIndicator: 'h-1 w-8 bg-primary rounded-full',
    subCategoriesTitle: 'text-sm font-bold text-foreground',
    subCategoriesList: 'space-y-1',
    subCategoryLink:
        'group flex items-center justify-between px-3 py-2.5 text-sm rounded-lg transition-all duration-200 text-foreground/80 hover:bg-muted/70 hover:text-primary hover:pr-4',
    subCategoryLinkActive:
        'bg-primary/10 dark:bg-primary/20 text-primary font-semibold shadow-sm',
    subCategoryChevron:
        'h-4 w-4 transition-all duration-200 opacity-0 group-hover:opacity-100',
    subCategoryChevronActive: 'text-primary opacity-100',
    itemsColumn: 'col-span-9 pr-2',
    itemsHeader:
        'flex items-center gap-3 mb-4 pb-3 border-b border-border',
    itemsIndicator:
        'h-8 w-1 bg-gradient-to-b from-primary to-accent rounded-full',
    itemsTitle: 'text-base font-bold text-foreground',
    itemsCount:
        'mr-auto px-2 py-0.5 text-xs bg-secondary text-secondary-foreground rounded-full',
    itemsGrid: 'grid grid-cols-4 gap-2',
    itemLink:
        'group px-3 py-2.5 text-sm text-foreground/80 rounded-lg hover:bg-muted/70 hover:text-primary hover:shadow-sm transition-all duration-200 flex items-center gap-2',
    itemDot:
        'h-1.5 w-1.5 rounded-full bg-muted-foreground/30 transition-all duration-200 group-hover:bg-primary group-hover:scale-150',
    emptyState:
        'flex flex-col items-center justify-center h-full text-muted-foreground',
    emptyIcon: 'h-12 w-12 mb-3 opacity-30',
    emptyText: 'text-sm font-medium',
} as const;
