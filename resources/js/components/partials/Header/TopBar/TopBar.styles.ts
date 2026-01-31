export const styles = {
    topBar:
        'hidden lg:block bg-gradient-to-l from-primary/8 via-secondary/5 to-accent/8 dark:from-primary/15 dark:via-background dark:to-accent/15 border-b border-border/30',
    container: 'container mx-auto px-4',
    inner: 'flex items-center justify-between h-8 text-xs',
    leftSection: 'flex items-center gap-5',
    contactItem:
        'group flex items-center gap-1.5 text-muted-foreground hover:text-primary transition-all duration-300 cursor-pointer',
    contactIcon:
        'h-3 w-3 transition-transform group-hover:scale-110',
    contactText: 'font-medium',
    divider: 'w-px h-3.5 bg-border/60',
    rightSection: 'flex items-center gap-3',
    trackOrderLink:
        'group flex items-center gap-1.5 px-2.5 py-1 text-muted-foreground hover:text-primary hover:bg-primary/5 rounded-md transition-all duration-300',
    trackOrderIcon:
        'h-3 w-3 transition-transform group-hover:scale-110',
    trackOrderText: 'font-medium',
    helpLink:
        'px-2.5 py-1 text-muted-foreground hover:text-primary hover:bg-primary/5 rounded-md transition-all duration-300 font-medium',
    themeToggleContainer: 'relative',
    themeToggleButton:
        'flex items-center gap-1 px-2 py-1 text-muted-foreground hover:text-primary hover:bg-primary/5 rounded-md transition-all duration-300',
    themeIcon: 'h-3.5 w-3.5',
    chevronIcon: 'h-2.5 w-2.5 transition-transform duration-200',
    chevronIconOpen: 'rotate-180',
    themeMenu:
        'absolute left-0 top-full mt-1.5 w-32 bg-card/95 backdrop-blur-xl border border-border/50 rounded-xl shadow-2xl z-50 overflow-hidden animate-in fade-in slide-in-from-top-2 duration-200',
    themeMenuInner: 'p-1',
    themeMenuItem:
        'w-full flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition-all duration-200 text-foreground hover:bg-muted',
    themeMenuItemActive: 'bg-primary/10 text-primary font-semibold',
    themeMenuItemIcon: 'h-4 w-4',
} as const;
