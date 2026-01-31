export const styles = {
    container: 'space-y-8',
    header:
        'flex items-center justify-between border-b border-border/50 pb-4',
    headerTitle: 'flex items-center gap-2 text-lg font-bold text-foreground',
    headerIcon: 'h-5 w-5 text-primary',
    clearButton:
        'flex items-center gap-1 rounded-md px-2 py-1 text-xs font-medium text-destructive transition-colors hover:bg-destructive/10 hover:text-destructive/80',
    clearIcon: 'h-3.5 w-3.5',
    section: 'space-y-4',
    sectionTitle:
        'flex items-center justify-between text-sm font-bold text-foreground',
    badge:
        'rounded-full bg-muted px-2 py-0.5 text-xs font-normal text-muted-foreground',
    categoriesList:
        'custom-scrollbar max-h-[280px] space-y-2 overflow-y-auto',
    categoryItem: 'flex items-center gap-2 py-1',
    categoryLabel:
        'flex-1 cursor-pointer text-sm text-foreground/80 transition-colors select-none hover:text-primary',
    categoryCount: 'text-[10px] text-muted-foreground',
    expandButton:
        'rounded p-1 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground',
    expandIcon: 'h-3.5 w-3.5 transition-transform duration-200',
    expandIconRotated: 'rotate-180',
    childrenContainer:
        'mt-1 mr-6 animate-in space-y-1.5 border-r-2 border-border/50 pr-3 duration-200 slide-in-from-top-2',
    childItem: 'flex items-center gap-2 py-0.5',
    childLabel:
        'flex-1 cursor-pointer text-xs text-muted-foreground transition-colors select-none hover:text-primary',
    childCount: 'text-[10px] text-muted-foreground',
    priceSection: 'space-y-4 border-t border-border/50 pt-4',
    priceTitle: 'text-sm font-bold text-foreground',
    priceSliderContainer: 'px-1',
    priceSlider: 'mb-6 cursor-pointer',
    priceInputs: 'flex items-center justify-between gap-4',
    priceInput:
        'flex flex-1 flex-col gap-1 rounded-lg border border-border bg-muted/20 p-2',
    priceInputLabel: 'text-[10px] text-muted-foreground',
    priceInputValue: 'text-sm font-bold text-foreground [direction:ltr]',
    brandsSection: 'space-y-4 border-t border-border/50 pt-4',
    brandsList:
        'custom-scrollbar max-h-[220px] space-y-2 overflow-y-auto',
    brandItem:
        'flex items-center gap-2 rounded-md p-1.5 transition-colors hover:bg-muted/30',
    brandLabel:
        'flex-1 cursor-pointer text-sm text-foreground/80 transition-colors select-none hover:text-primary',
    brandCount:
        'rounded bg-muted px-1.5 py-0.5 text-[10px] text-muted-foreground',
} as const;
