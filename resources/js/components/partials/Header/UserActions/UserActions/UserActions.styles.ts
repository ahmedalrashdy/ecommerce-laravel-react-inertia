export const styles = {
    wrapper: 'flex items-center gap-1.5',
    desktopUserMenuContainer: 'hidden lg:block relative',
    userMenuButton:
        'group flex items-center gap-2 h-10 px-3 text-sm font-semibold text-foreground hover:bg-primary/10 hover:text-primary rounded-xl transition-all duration-300',
    userMenuIcon:
        'h-4 w-4 transition-transform duration-300 group-hover:scale-110',
    userMenuChevron: 'h-3.5 w-3.5 transition-transform duration-300',
    userMenuChevronOpen: 'rotate-180',
    userMenuDropdown:
        'absolute left-0 top-full mt-2 w-52 bg-card/95 backdrop-blur-xl border border-border/50 rounded-xl shadow-2xl z-50 overflow-hidden animate-in fade-in slide-in-from-top-2 duration-200',
    userMenuDropdownInner: 'p-1.5',
    userMenuLink:
        'group flex items-center gap-3 px-3 py-2.5 text-sm text-foreground rounded-lg transition-all duration-200',
    userMenuLinkLogin: 'hover:bg-primary/10 hover:text-primary',
    userMenuLinkRegister: 'hover:bg-accent/10 hover:text-accent',
    userMenuLinkHelp: 'text-orange-500 hover:bg-orange-500/10',
    userMenuLinkLogout: 'w-full hover:bg-destructive/10 hover:text-destructive',
    userMenuLinkForm: 'w-full',
    userMenuLinkIconWrapper:
        'flex items-center justify-center h-8 w-8 rounded-lg transition-colors bg-muted/50 group-hover:bg-muted',
    userMenuLinkIconWrapperLogin: 'bg-primary/10 group-hover:bg-primary/20',
    userMenuLinkIconWrapperRegister: 'bg-accent/10 group-hover:bg-accent/20',
    userMenuLinkIconWrapperHelp:
        'bg-orange-500/10 group-hover:bg-orange-500/20',
    userMenuLinkIcon: 'h-4 w-4',
    userMenuLinkIconHelp: 'h-4 w-4 text-orange-500',
    userMenuLinkText: 'font-medium',
    mobileUserLink:
        'lg:hidden flex items-center justify-center h-10 w-10 text-foreground hover:bg-primary/10 hover:text-primary rounded-xl transition-all duration-300',
    mobileUserIcon: 'h-4 w-4',
    wishlistLink:
        'group relative flex items-center justify-center h-10 w-10 text-foreground hover:bg-primary/10 hover:text-primary rounded-xl transition-all duration-300',
    wishlistIcon:
        'h-4.5 w-4.5 transition-all duration-300 group-hover:fill-current group-hover:scale-110',
    wishlistBadge:
        'absolute -top-0.5 -right-0.5 h-5 w-5 flex items-center justify-center text-[10px] bg-gradient-to-br from-primary to-primary/80 text-primary-foreground rounded-full font-bold shadow-md',
    cartLink:
        'group relative flex items-center justify-center h-10 w-10 text-foreground hover:bg-primary/10 hover:text-primary rounded-xl transition-all duration-300',
    cartIcon:
        'h-4.5 w-4.5 transition-transform duration-300 group-hover:scale-110',
    cartBadge:
        'absolute -top-0.5 -right-0.5 h-5 w-5 flex items-center justify-center text-[10px] bg-gradient-to-br from-destructive to-badge-sale text-white rounded-full font-bold shadow-md animate-pulse',
} as const;
