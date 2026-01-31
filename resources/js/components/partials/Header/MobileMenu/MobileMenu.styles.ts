export const styles = {
    backdrop:
        'fixed inset-0 bg-black/50 z-50 lg:hidden animate-in fade-in duration-200',
    sidebar:
        'fixed top-0 right-0 h-full w-full sm:w-[380px] bg-card border-l border-border z-50 lg:hidden flex flex-col animate-in slide-in-from-right duration-300',
    header:
        'p-4 border-b border-border bg-gradient-to-r from-primary/10 to-accent/10 flex items-center justify-between',
    headerTitle: 'text-lg font-bold text-foreground',
    closeButton:
        'flex items-center justify-center h-8 w-8 text-muted-foreground hover:text-foreground rounded-md hover:bg-muted transition-colors',
    closeIcon: 'h-5 w-5',
    content: 'flex-1 overflow-y-auto p-4',
    categoryItem: '',
    categoryItemIndented: 'pr-4',
    categoryHeader: 'flex items-center justify-between',
    categoryLink:
        'flex-1 py-2 text-foreground hover:text-primary transition-colors',
    categoryLinkLevel0: 'font-medium text-base',
    categoryLinkLevel1: 'text-sm',
    categoryLinkLevel2: 'text-xs',
    expandButton:
        'p-2 text-muted-foreground hover:text-primary transition-colors',
    expandIcon: 'h-4 w-4 transition-transform duration-200',
    expandIconOpen: 'rotate-180',
    subCategories: 'pr-2 space-y-1 mt-1',
} as const;
