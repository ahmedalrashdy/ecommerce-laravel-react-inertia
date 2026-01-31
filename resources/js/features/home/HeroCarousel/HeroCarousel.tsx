import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import { HeroSlide } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import {
    ArrowLeft,
    ChevronLeft,
    ChevronRight,
    Flame,
    Pause,
    Play,
    ShoppingBag,
    Sparkles,
    Star,
    Zap,
} from 'lucide-react';
import * as React from 'react';
import { styles } from './HeroCarousel.styles';

const BadgeIcon: React.FC<{ icon: string }> = ({ icon }) => {
    switch (icon) {
        case 'sparkles':
            return <Sparkles className={styles.badgeIcon} />;
        case 'flame':
            return <Flame className={styles.badgeIcon} />;
        case 'star':
            return <Star className={styles.badgeIcon} />;
        case 'zap':
            return <Zap className={styles.badgeIcon} />;
        default:
            return null;
    }
};

export default function HeroCarousel() {
    const { heroSlides } = usePage<{ heroSlides: HeroSlide[] }>().props;

    const [currentSlide, setCurrentSlide] = React.useState(0);
    const [isAutoPlaying, setIsAutoPlaying] = React.useState(true);
    const [isPaused, setIsPaused] = React.useState(false);
    const AUTO_PLAY_DURATION = 5000;

    const touchStartX = React.useRef<number | null>(null);
    const touchEndX = React.useRef<number | null>(null);

    const slide = heroSlides[currentSlide];

    const goToNext = React.useCallback(() => {
        setCurrentSlide((prev) => (prev + 1) % heroSlides.length);
    }, []);

    const goToPrev = React.useCallback(() => {
        setCurrentSlide(
            (prev) => (prev - 1 + heroSlides.length) % heroSlides.length,
        );
    }, []);

    const goToIndex = (index: number) => {
        setCurrentSlide(index);
    };

    React.useEffect(() => {
        if (isPaused || !isAutoPlaying) return;
        const timer = setInterval(() => {
            goToNext();
        }, AUTO_PLAY_DURATION);
        return () => clearInterval(timer);
    }, [isPaused, isAutoPlaying, goToNext, currentSlide]);

    // Touch logic omitted for brevity (same as before)
    const handleTouchStart = (e: React.TouchEvent) => {
        setIsPaused(true);
        touchStartX.current = e.targetTouches[0].clientX;
    };
    const handleTouchMove = (e: React.TouchEvent) => {
        touchEndX.current = e.targetTouches[0].clientX;
    };
    const handleTouchEnd = () => {
        setIsPaused(false);
        if (!touchStartX.current || !touchEndX.current) return;
        const distance = touchStartX.current - touchEndX.current;
        if (distance > 50) goToNext();
        if (distance < -50) goToPrev();
        touchStartX.current = null;
        touchEndX.current = null;
    };

    return (
        <section
            dir="rtl"
            className={styles.section}
            onMouseEnter={() => setIsPaused(true)}
            onMouseLeave={() => setIsPaused(false)}
            onTouchStart={handleTouchStart}
            onTouchMove={handleTouchMove}
            onTouchEnd={handleTouchEnd}
        >
            {/* Background Slides */}
            {heroSlides.map((item, index) => (
                <div
                    key={item.id}
                    className={cn(
                        styles.slideContainer,
                        index === currentSlide
                            ? styles.slideActive
                            : styles.slideInactive,
                    )}
                >
                    <div className={styles.slideOverlay} />
                    <div className={cn(styles.slideGradient, item.color)} />
                    <img
                        src={item.image}
                        alt={item.title}
                        className={cn(
                            styles.slideImage,
                            index === currentSlide && !isPaused
                                ? styles.slideImageZoomed
                                : styles.slideImageNormal,
                        )}
                        loading={index === 0 ? "eager" : "lazy"}
                        fetchPriority={index === 0 ? "high" : "auto"}
                        decoding="async"
                    />
                </div>
            ))}

            {/* Content Container */}
            {/* 🛠️ التعديل الأول: إضافة pb-32 في الجوال لرفع النص بعيداً عن الصور المصغرة */}
            <div className={styles.contentContainer}>
                <div className={styles.contentInner}>
                    <div className={styles.contentWrapper}>
                        <div
                            key={slide.id}
                            className={styles.slideContent}
                        >
                            {slide.badge && (
                                <div className="animate-in delay-100 duration-700 ease-out fade-in slide-in-from-bottom-4">
                                    <span
                                        className={cn(
                                            styles.badge,
                                            slide.color,
                                        )}
                                    >
                                        <BadgeIcon icon={slide.badge.icon} />
                                        {slide.badge.text}
                                    </span>
                                </div>
                            )}

                            <div className={styles.titleGroup}>
                                <h3 className={styles.subtitle}>
                                    {slide.subtitle}
                                </h3>
                                {/* 🛠️ تصغير حجم الخط قليلاً في الجوال ليتناسب مع المساحة */}
                                <h1 className={styles.title}>{slide.title}</h1>
                            </div>

                            <p className={styles.description}>
                                {slide.description}
                            </p>

                            <div className={styles.actions}>
                                <Button
                                    asChild
                                    size="lg"
                                    className={styles.primaryButton}
                                >
                                    <Link
                                        href={slide.primaryCTA.href}
                                        className={styles.primaryButtonLink}
                                    >
                                        <ShoppingBag
                                            className={styles.primaryButtonIcon}
                                        />
                                        {slide.primaryCTA.text}
                                    </Link>
                                </Button>
                                {slide.secondaryCTA && (
                                    <Button
                                        asChild
                                        size="lg"
                                        variant="outline"
                                        className={styles.secondaryButton}
                                    >
                                        <Link
                                            href={slide.secondaryCTA.href}
                                            className={
                                                styles.secondaryButtonLink
                                            }
                                        >
                                            {slide.secondaryCTA.text}
                                            <ArrowLeft
                                                className={
                                                    styles.secondaryButtonIcon
                                                }
                                            />
                                        </Link>
                                    </Button>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Desktop Navigation Arrows (Hidden on Mobile) */}
            <button
                onClick={goToPrev}
                className={cn(styles.navButton, styles.navButtonPrev)}
            >
                <ChevronRight className={styles.navButtonIcon} />
            </button>
            <button
                onClick={goToNext}
                className={cn(styles.navButton, styles.navButtonNext)}
            >
                <ChevronLeft className={styles.navButtonIcon} />
            </button>

            {/* 🛠️ التعديل الثاني: تدرج لوني في الأسفل لضمان وضوح الأزرار فوق الخلفية */}
            <div className={styles.bottomGradient} />

            {/* Bottom Controls Area */}
            <div className={styles.controlsContainer}>
                {/* 🛠️ التعديل الثالث: تغيير الترتيب (flex-col-reverse) للجوال لجعل الصور في الأسفل تماماً */}
                <div className={styles.controlsInner}>
                    {/* Thumbnails */}
                    <div className={styles.thumbnails}>
                        {heroSlides.map((item, idx) => (
                            <button
                                key={item.id}
                                onClick={() => goToIndex(idx)}
                                className={cn(
                                    styles.thumbnail,
                                    idx === currentSlide
                                        ? styles.thumbnailActive
                                        : styles.thumbnailInactive,
                                )}
                            >
                                <img
                                    src={item.image}
                                    alt=""
                                    loading='lazy'
                                    decoding='async'
                                    className={styles.thumbnailImage}
                                />
                                {/* Dark overlay on inactive thumbnails */}
                                <div
                                    className={cn(
                                        styles.thumbnailOverlay,
                                        idx === currentSlide
                                            ? styles.thumbnailOverlayActive
                                            : styles.thumbnailOverlayInactive,
                                    )}
                                />

                                {idx === currentSlide &&
                                    isAutoPlaying &&
                                    !isPaused && (
                                        <div className={styles.progressBar}>
                                            <div
                                                className={
                                                    styles.progressBarFill
                                                }
                                                style={{
                                                    animation: `progress ${AUTO_PLAY_DURATION}ms linear forwards`,
                                                }}
                                            />
                                        </div>
                                    )}
                            </button>
                        ))}
                    </div>

                    {/* Play/Counter - مظهر جديد أكثر أناقة */}
                    <div className={styles.playCounter}>
                        <button
                            onClick={() => setIsAutoPlaying(!isAutoPlaying)}
                            className={styles.playButton}
                        >
                            {isAutoPlaying && !isPaused ? (
                                <Pause className={styles.playButtonIcon} />
                            ) : (
                                <Play className={styles.playButtonIcon} />
                            )}
                        </button>
                        <div className={styles.divider} /> {/* فاصل عمودي */}
                        <div className={styles.counter}>
                            <span className={styles.counterCurrent}>
                                0{currentSlide + 1}
                            </span>
                            <span>/</span>
                            <span>0{heroSlides.length}</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
