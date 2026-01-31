export const styles = {
    section: 'bg-background py-6',
    container: 'container mx-auto px-4',
    header:
        'mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between',
    headerContent: 'flex-1',
    title: 'mb-2 text-3xl leading-tight font-bold text-foreground md:text-4xl',
    description: 'text-sm text-muted-foreground md:text-base',
    navGroup:
        'flex w-full items-center justify-between gap-4 md:w-auto md:justify-end',
    viewAllLink:
        'group inline-flex items-center gap-1 text-sm font-medium text-primary transition-colors hover:text-primary/80',
    viewAllIcon:
        'h-4 w-4 transition-transform group-hover:-translate-x-1',
    arrows: 'hidden items-center gap-2 md:flex',
    arrowButton:
        'h-10 w-10 rounded-full border-foreground/10 text-foreground transition-all hover:border-foreground/20 hover:bg-foreground/5 disabled:opacity-30 dark:border-zinc-700 dark:text-zinc-100 dark:hover:border-zinc-600 dark:hover:bg-zinc-800',
    arrowIcon: 'h-5 w-5',
    sliderContainer: 'relative',
    gradientRight:
        'pointer-events-none absolute top-0 right-0 bottom-4 z-10 hidden w-24 bg-gradient-to-l from-background to-transparent transition-opacity duration-300 md:block',
    gradientRightVisible: 'opacity-100',
    gradientRightHidden: 'opacity-0',
    gradientLeft:
        'pointer-events-none absolute top-0 bottom-4 left-0 z-10 hidden w-24 bg-gradient-to-r from-background to-transparent transition-opacity duration-300 md:block',
    gradientLeftVisible: 'opacity-100',
    gradientLeftHidden: 'opacity-0',
    scrollContainer:
        '-mx-4 scrollbar-hide flex snap-x snap-mandatory gap-3 overflow-x-auto scroll-smooth px-4 pb-4 md:mx-0 md:gap-6 md:px-0',
    productWrapper: 'w-[170px] shrink-0 snap-start md:w-64 lg:w-72',
} as const;
