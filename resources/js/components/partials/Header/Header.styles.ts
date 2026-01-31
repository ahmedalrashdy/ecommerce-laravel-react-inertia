export const styles = {
    header:
        'w-full bg-background/95 backdrop-blur-md relative z-50 transition-all duration-500 border-b border-border/30',
    mainSection:
        'border-b border-border/40 bg-gradient-to-l from-card/60 via-background to-card/60 dark:from-muted/5 dark:via-background dark:to-muted/5',
    mainContainer: 'container mx-auto px-4',
    mainInner: 'flex h-14 md:h-16 items-center justify-between gap-6',
    leftSection: 'flex items-center gap-3 shrink-0',
    mobileMenuButton:
        'lg:hidden flex items-center justify-center h-10 w-10 text-foreground hover:bg-primary/10 hover:text-primary rounded-xl transition-all duration-300',
    mobileMenuIcon: 'h-5 w-5',
    logoLink: 'group flex items-center gap-3',
    logoIconWrapper:
        'relative flex items-center justify-center h-11 w-11 bg-gradient-to-br from-primary via-primary/90 to-accent rounded-xl shadow-lg transition-all duration-500 group-hover:shadow-xl group-hover:shadow-primary/25 group-hover:scale-105',
    logoIcon:
        'h-5 w-5 text-white transition-transform duration-300 group-hover:scale-110',
    logoOverlay:
        'absolute inset-0 rounded-xl bg-white/20 opacity-0 transition-opacity duration-300 group-hover:opacity-100',
    logoTextWrapper: 'hidden md:flex flex-col',
    logoText:
        'text-2xl font-bold bg-gradient-to-l from-primary via-primary/80 to-accent bg-clip-text text-transparent transition-all duration-500 group-hover:from-accent group-hover:to-primary',
    logoSubtext:
        'text-[10px] text-muted-foreground -mt-0.5 font-medium tracking-wide',
    searchSection: 'hidden md:flex flex-1 justify-center max-w-2xl',
    actionsSection: 'shrink-0',
    mobileSearchSection:
        'md:hidden px-4 py-3 border-b border-border bg-muted/30 dark:bg-muted/10',
    spacer: 'lg:hidden h-16',
} as const;
