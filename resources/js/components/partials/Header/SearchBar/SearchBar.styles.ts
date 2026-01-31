export const styles = {
    container: 'relative w-full',
    form: 'relative w-full',
    inputWrapper: 'relative flex items-center',
    input:
        'w-full h-11 pr-12 pl-10 bg-muted/40 dark:bg-muted/20 border-2 border-border/50 focus:bg-background focus:border-primary focus:shadow-lg focus:shadow-primary/10 rounded-xl transition-all duration-300 outline-none text-sm text-foreground placeholder:text-muted-foreground',
    submitButton:
        'absolute right-1.5 flex items-center justify-center h-8 w-8 rounded-lg bg-gradient-to-br from-primary to-primary/80 text-primary-foreground shadow-md transition-all duration-300 hover:from-primary/90 hover:to-primary/70 hover:shadow-lg hover:scale-105',
    submitIcon: 'h-4 w-4',
    clearButton:
        'absolute left-3 text-muted-foreground hover:text-destructive transition-colors duration-200',
    clearIcon: 'h-4 w-4',
    suggestionsDropdown:
        'absolute top-full left-0 right-0 mt-2 bg-background border border-border rounded-xl shadow-xl overflow-hidden z-50 max-h-[70vh] overflow-y-auto animate-in fade-in-0 slide-in-from-top-2 duration-200',
    loadingRow: 'flex items-center gap-2 px-4 py-3 text-sm text-muted-foreground',
    loadingIcon: 'h-4 w-4',
    emptyState: 'px-4 py-3 text-sm text-muted-foreground',
    suggestionGroup: 'border-b border-border/50 last:border-b-0',
    groupHeader:
        'flex items-center gap-2 px-4 py-2 text-xs font-semibold text-muted-foreground uppercase tracking-wide bg-muted/30 dark:bg-muted/10',
    suggestionItem:
        'flex items-center gap-3 w-full px-4 py-3 text-right transition-colors duration-150 cursor-pointer hover:bg-muted/50 dark:hover:bg-muted/30 focus:outline-none focus:bg-muted/50 dark:focus:bg-muted/30',
    suggestionItemSelected: 'bg-primary/10 dark:bg-primary/20',
    suggestionImage:
        'w-10 h-10 rounded-lg object-cover bg-muted shrink-0',
    suggestionImagePlaceholder:
        'w-10 h-10 rounded-lg bg-muted/50 dark:bg-muted/30 flex items-center justify-center text-muted-foreground shrink-0',
    suggestionContent: 'flex-1 min-w-0 flex flex-col gap-0.5',
    suggestionName: 'text-sm font-medium text-foreground truncate',
    suggestionPrice: 'text-xs text-primary font-semibold',
    viewAllButton:
        'flex items-center justify-center gap-2 w-full px-4 py-3 text-sm font-medium text-primary bg-muted/30 dark:bg-muted/10 transition-colors duration-150 hover:bg-primary/10 dark:hover:bg-primary/20 focus:outline-none focus:bg-primary/10 dark:focus:bg-primary/20 border-t border-border/50',
} as const;
