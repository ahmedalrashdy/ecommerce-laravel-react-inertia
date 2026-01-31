export const styles = {
    section: 'bg-background py-12 md:py-16',
    container: 'container mx-auto px-4',
    header:
        'mb-8 flex flex-col md:flex-row md:items-center md:justify-between',
    headerContent: 'mb-4 text-center md:mb-0 md:text-right',
    title: 'mb-2 text-3xl font-bold text-foreground md:text-4xl',
    description: 'text-muted-foreground',
    navButtons: 'hidden items-center gap-2 md:flex',
    navButton:
        'h-10 w-10 rounded-full border-foreground/10 text-foreground transition-all hover:border-foreground/20 hover:bg-foreground/5 disabled:opacity-30 dark:border-zinc-700 dark:text-zinc-100 dark:hover:border-zinc-600 dark:hover:bg-zinc-800',
    navButtonIcon: 'h-5 w-5',
    sliderContainer: 'relative',
    gradientRight:
        'pointer-events-none absolute top-0 right-0 bottom-4 z-10 hidden w-20 bg-gradient-to-l from-background to-transparent transition-opacity duration-300 md:block',
    gradientRightVisible: 'opacity-100',
    gradientRightHidden: 'opacity-0',
    gradientLeft:
        'pointer-events-none absolute top-0 bottom-4 left-0 z-10 hidden w-20 bg-gradient-to-r from-background to-transparent transition-opacity duration-300 md:block',
    gradientLeftVisible: 'opacity-100',
    gradientLeftHidden: 'opacity-0',
    scrollContainer:
        'scrollbar-hide flex snap-x snap-mandatory gap-4 overflow-x-auto scroll-smooth pb-4',
} as const;
