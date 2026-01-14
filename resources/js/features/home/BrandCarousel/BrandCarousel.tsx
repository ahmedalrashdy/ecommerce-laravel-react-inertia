import { storageUrl } from '@/lib/utils';
import { show as brandShow } from '@/routes/store/brand';
import { Link, usePage } from '@inertiajs/react';
import 'swiper/css';
import 'swiper/css/free-mode';
import { Autoplay, FreeMode } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/react';
import styles from './BrandCarousel.module.css';

export const BrandCarousel: React.FC = () => {
    const { featuredBrands } = usePage<{
        featuredBrands: App.Data.Basic.BrandData[];
    }>().props;
    return (
        <section className={styles.section}>
            <div className={styles.container}>
                {/* Section Header */}
                <div className={styles.header}>
                    <h2 className={styles.title}>العلامات التجارية الشريكة</h2>
                    <p className={styles.description}>
                        نتعاون مع أفضل العلامات التجارية العالمية
                    </p>
                </div>
                {/* Brands Swiper */}
                <Swiper
                    modules={[Autoplay, FreeMode]}
                    spaceBetween={40}
                    slidesPerView={2}
                    loop={true}
                    freeMode={true}
                    autoplay={{
                        delay: 0,
                        disableOnInteraction: false,
                        pauseOnMouseEnter: true,
                    }}
                    speed={3000}
                    breakpoints={{
                        640: {
                            slidesPerView: 3,
                            spaceBetween: 50,
                        },
                        768: {
                            slidesPerView: 4,
                            spaceBetween: 60,
                        },
                        1024: {
                            slidesPerView: 6,
                            spaceBetween: 80,
                        },
                    }}
                    className="brand-swiper"
                >
                    {featuredBrands.map((brand, index) => (
                        <SwiperSlide key={`${brand.id}-${index}`}>
                            <Link
                                href={brandShow.url(brand.slug)}
                                className={styles.brandLink}
                            >
                                <img
                                    {...(brand.image && {
                                        src: storageUrl(brand.image),
                                    })}
                                    alt={brand.name}
                                    className={styles.brandImage}
                                />
                            </Link>
                        </SwiperSlide>
                    ))}
                </Swiper>
            </div>
        </section>
    );
};
