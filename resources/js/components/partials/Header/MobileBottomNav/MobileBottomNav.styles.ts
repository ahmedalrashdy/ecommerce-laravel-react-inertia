export const styles = {
    wrapper:
        'fixed right-0 bottom-0 left-0 z-50 border-t border-border bg-card/95 shadow-2xl backdrop-blur-sm lg:hidden dark:bg-card/98',
    grid: 'grid h-16 grid-cols-4',
    navItem:
        'flex flex-col items-center justify-center gap-1 text-xs font-medium text-muted-foreground transition-colors hover:text-primary',
    navItemIconWrapper: 'relative',
    navItemIcon: 'h-5 w-5',
    badge:
        'absolute -top-2 -right-2 flex h-4 w-4 items-center justify-center rounded-full text-[9px] font-bold',
    badgeDestructive: 'bg-destructive text-destructive-foreground',
    badgePrimary: 'bg-primary text-primary-foreground',
} as const;
