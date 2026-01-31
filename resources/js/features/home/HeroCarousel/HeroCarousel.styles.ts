export const styles = {
    section:
        'group relative h-[600px] w-full overflow-hidden bg-black select-none sm:h-[600px] lg:h-[700px]',
    slideContainer:
        'absolute inset-0 h-full w-full transition-all duration-1000 ease-in-out',
    slideActive: 'z-10 opacity-100',
    slideInactive: 'z-0 opacity-0',
    slideOverlay: 'absolute inset-0 z-10 bg-black/40',
    slideGradient:
        'absolute inset-0 z-10 bg-gradient-to-r to-transparent opacity-40 mix-blend-multiply',
    slideImage:
        'h-full w-full object-cover transition-transform duration-[10000ms] ease-linear will-change-transform',
    slideImageZoomed: 'scale-110',
    slideImageNormal: 'scale-100',
    contentContainer:
        'absolute inset-0 z-20 flex items-center pb-32 sm:pb-0',
    contentInner:
        'container mx-auto flex h-full flex-col justify-center px-4 sm:px-6 lg:px-12',
    contentWrapper: 'max-w-3xl space-y-6 pt-10 sm:pt-0',
    slideContent: 'space-y-4 sm:space-y-6',
    badge:
        'inline-flex items-center gap-1.5 rounded-full border border-white/20 bg-gradient-to-r px-3 py-1 text-xs font-bold text-white shadow-lg backdrop-blur-md sm:text-sm',
    badgeIcon: 'h-3.5 w-3.5',
    titleGroup: 'space-y-2',
    subtitle:
        'animate-in text-lg font-medium text-white/90 duration-700 fade-in slide-in-from-bottom-6 sm:text-2xl',
    title:
        'animate-in text-3xl leading-[1.1] font-black tracking-tight text-white duration-700 fade-in slide-in-from-bottom-8 sm:text-5xl lg:text-7xl',
    description:
        'line-clamp-2 max-w-xl animate-in text-sm leading-relaxed text-white/80 duration-700 fade-in slide-in-from-bottom-8 sm:line-clamp-none sm:text-lg',
    actions:
        'flex animate-in flex-wrap gap-3 pt-2 duration-700 slide-in-from-bottom-10 fade-in sm:gap-4',
    primaryButton:
        'h-10 rounded-full border-0 bg-white px-6 text-sm text-black shadow-xl shadow-black/20 transition-all duration-300 hover:scale-105 hover:bg-white/90 sm:h-12 sm:px-8 sm:text-base',
    primaryButtonLink: 'flex items-center gap-2',
    primaryButtonIcon: 'h-4 w-4',
    secondaryButton:
        'h-10 rounded-full border-white/30 px-6 text-sm text-white backdrop-blur-sm transition-all duration-300 hover:border-white hover:bg-white/10 hover:text-white sm:h-12 sm:px-8 sm:text-base',
    secondaryButtonLink: 'flex items-center gap-2',
    secondaryButtonIcon: 'h-4 w-4',
    navButton:
        'absolute top-1/2 z-30 hidden -translate-y-1/2 rounded-full border border-white/10 bg-black/20 p-3 text-white opacity-0 backdrop-blur-md transition-all duration-300 hover:scale-110 hover:bg-white hover:text-black sm:flex group-hover:opacity-100',
    navButtonPrev: 'right-4',
    navButtonNext: 'left-4',
    navButtonIcon: 'h-6 w-6',
    bottomGradient:
        'pointer-events-none absolute right-0 bottom-0 left-0 z-20 h-32 bg-gradient-to-t from-black/90 to-transparent',
    controlsContainer:
        'absolute right-0 bottom-4 left-0 z-30 px-4 sm:bottom-8',
    controlsInner:
        'container mx-auto flex flex-col-reverse items-center justify-between gap-4 sm:flex-row',
    thumbnails:
        'scrollbar-hide flex w-full justify-center gap-2 overflow-x-auto pb-1 sm:w-auto sm:justify-start sm:gap-3 sm:pb-0',
    thumbnail:
        'relative h-10 w-14 shrink-0 overflow-hidden rounded-md border-2 transition-all duration-300 sm:h-16 sm:w-24 sm:rounded-lg',
    thumbnailActive: 'w-16 border-white shadow-lg shadow-black/50 sm:w-32',
    thumbnailInactive: 'border-transparent opacity-50 hover:opacity-100',
    thumbnailImage: 'h-full w-full object-cover',
    thumbnailOverlay: 'absolute inset-0 bg-black/40 transition-opacity',
    thumbnailOverlayActive: 'opacity-0',
    thumbnailOverlayInactive: 'opacity-100',
    progressBar:
        'absolute bottom-0 left-0 z-10 h-0.5 w-full bg-white/50 sm:h-1',
    progressBarFill: 'h-full origin-right bg-white',
    playCounter:
        'flex items-center gap-3 rounded-full border border-white/10 bg-black/20 px-4 py-1.5 backdrop-blur-md sm:border-0 sm:bg-transparent sm:backdrop-blur-none',
    playButton: 'p-1 text-white transition-colors hover:text-white/80',
    playButtonIcon: 'h-4 w-4',
    divider: 'h-3 w-[1px] bg-white/20',
    counter: 'flex gap-1 font-mono text-xs text-white/70',
    counterCurrent: 'font-bold text-white',
} as const;
