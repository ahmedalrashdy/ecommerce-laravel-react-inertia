export const styles = {
    section: 'bg-muted/20 py-12 md:py-16 dark:bg-muted/10',
    container: 'container mx-auto px-4',
    header: 'mb-10 text-center',
    title: 'mb-3 text-3xl font-bold text-foreground md:text-4xl',
    description: 'mx-auto max-w-2xl text-muted-foreground',
    grid: 'grid gap-6 md:grid-cols-2',
    card: 'group relative block overflow-hidden rounded-2xl border border-border bg-card shadow-md transition-smooth hover:shadow-2xl',
    imageContainer: 'relative h-80 overflow-hidden md:h-96',
    image:
        'h-full w-full object-cover transition-transform duration-500 group-hover:scale-110',
    gradientOverlay:
        'absolute inset-0 bg-gradient-to-t from-black/85 via-black/50 to-transparent dark:from-black/90 dark:via-black/60',
    badge:
        'absolute top-6 right-6 rounded-full bg-badge-featured px-4 py-2 text-sm font-bold text-white shadow-lg',
    content: 'absolute right-0 bottom-0 left-0 p-8 text-white',
    contentTitle: 'mb-3 text-3xl font-bold md:text-4xl',
    contentDescription: 'mb-6 line-clamp-2 text-base opacity-90 md:text-lg',
    actionButton: 'gap-2 transition-smooth hover:scale-105 group-hover:gap-4',
    actionButtonIcon: 'h-5 w-5',
} as const;
