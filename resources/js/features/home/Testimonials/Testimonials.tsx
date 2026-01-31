import { TestimonialCardSkeleton } from '@/components/feedback/skeletons/TestimonialCardSkeleton';
import { cn } from '@/lib/utils';
import { usePage, WhenVisible } from '@inertiajs/react';
import { Quote, Star } from 'lucide-react';
import * as React from 'react';
import 'swiper/css';
import 'swiper/css/pagination';
import { Autoplay, Pagination } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/react';
import { styles } from './Testimonials.styles';

export interface Testimonial {
    id: number;
    name: string;
    avatar: string;
    rating: number;
    comment: string;
    date: string;
    verified: boolean;
}

interface PageProps {
    testimonials?: Testimonial[];
    [key: string]: unknown;
}

export const Testimonials: React.FC = () => {
    const { testimonials } = usePage<PageProps>().props;

    return (
        <section className={styles.section}>
            <div className={styles.container}>
                {/* Section Header */}
                <div className={styles.header}>
                    <h2 className={styles.title}>آراء العملاء</h2>
                    <p className={styles.description}>
                        اكتشف ما يقوله عملاؤنا عن تجربتهم معنا
                    </p>
                </div>

                {/* Testimonials Swiper with WhenVisible */}
                <WhenVisible
                    data="testimonials"
                    fallback={() => (
                        <SwiperWrapper loop={false}>
                            {Array.from({ length: 3 }).map((_, index) => (
                                <SwiperSlide key={index}>
                                    <TestimonialCardSkeleton />
                                </SwiperSlide>
                            ))}
                        </SwiperWrapper>
                    )}
                >
                    {() => {
                        if (!testimonials || testimonials.length === 0) {
                            return (
                                <div className="py-12 text-center text-muted-foreground">
                                    <p>لا توجد آراء متاحة حالياً</p>
                                </div>
                            );
                        }

                        return (
                            <SwiperWrapper loop={testimonials.length > 3}>
                                {testimonials.map((testimonial) => (
                                    <SwiperSlide key={testimonial.id}>
                                        <TestimonialCard
                                            testimonial={testimonial}
                                        />
                                    </SwiperSlide>
                                ))}
                            </SwiperWrapper>
                        );
                    }}
                </WhenVisible>
            </div>

            <style>{`
                .${styles.swiper} .swiper-pagination-bullet {
                    width: 10px;
                    height: 10px;
                    background: hsl(var(--primary));
                    opacity: 0.3;
                }
                .${styles.swiper} .swiper-pagination-bullet-active {
                    opacity: 1;
                    width: 28px;
                    border-radius: 5px;
                }
            `}</style>
        </section>
    );
};

const SwiperWrapper = ({
    children,
    loop,
}: {
    children: React.ReactNode;
    loop: boolean;
}) => {
    return (
        <Swiper
            modules={[Autoplay, Pagination]}
            spaceBetween={24}
            slidesPerView={1}
            loop={loop}
            autoplay={{
                delay: 4000,
                disableOnInteraction: false,
            }}
            pagination={{
                clickable: true,
                dynamicBullets: true,
            }}
            breakpoints={{
                640: {
                    slidesPerView: 2,
                },
                1024: {
                    slidesPerView: 3,
                },
            }}
            className={cn(styles.swiperWrapperClass, styles.swiper)}
        >
            {children}
        </Swiper>
    );
};
const TestimonialCard: React.FC<{ testimonial: Testimonial }> = ({
    testimonial,
}) => {
    return (
        <div className={styles.card}>
            {/* Quote Icon */}
            <div className={styles.quoteIcon}>
                <Quote className={styles.quoteIconSvg} />
            </div>

            {/* Rating */}
            <div className={styles.rating}>
                {Array.from({ length: 5 }).map((_, index) => (
                    <Star
                        key={index}
                        className={`${styles.star} ${
                            index < testimonial.rating
                                ? styles.starFilled
                                : styles.starEmpty
                        }`}
                    />
                ))}
            </div>

            {/* Comment */}
            <p className={styles.comment}>{testimonial.comment}</p>

            {/* User Info */}
            <div className={styles.userInfo}>
                <img
                    src={testimonial.avatar}
                    alt={testimonial.name}
                    className={styles.avatar}
                       loading="lazy"
                    decoding="async"
                />
                <div className={styles.userDetails}>
                    <div className={styles.userNameRow}>
                        <h4 className={styles.userName}>{testimonial.name}</h4>
                        {testimonial.verified && (
                            <span className={styles.verifiedBadge}>موثق</span>
                        )}
                    </div>
                    <p className={styles.date}>{testimonial.date}</p>
                </div>
            </div>
        </div>
    );
};
