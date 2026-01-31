export const styles = {
    section: 'border-y border-border bg-background py-12 md:py-16',
    container: 'container mx-auto px-4',
    header: 'mb-10 text-center',
    title: 'mb-3 text-3xl font-bold text-foreground md:text-4xl',
    description: 'mx-auto max-w-2xl text-muted-foreground',
    brandLink:
        'group flex h-24 items-center justify-center opacity-60 grayscale transition-all duration-300 hover:opacity-100 hover:grayscale-0',
    brandImage:
        'max-h-16 max-w-full object-contain transition-transform group-hover:scale-110',
} as const;
