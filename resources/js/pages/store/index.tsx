import {
    BestSellers,
    BrandCarousel,
    CategorySpotlight,
    HeroCarousel,
    NewArrivals,
    Testimonials,
    TopRated,
} from '@/features/home';
import { Newsletter } from '@/features/home/NewsLetters/Newsletter';
import StoreLayout from '@/layouts/StoreLayout';

export default function Index() {
    return (
        <StoreLayout>
            <div className="flex flex-col">
                {/* Hero Section with Custom Carousel (No Swiper) */}
                <section className="w-full">
                    <HeroCarousel />
                </section>

                {/* Flash Deals with Countdown */}
                {/* <section className="w-full">
                    <FlashDeals />
                </section> */}

                {/* Category Spotlight */}
                <section className="w-full">
                    <CategorySpotlight />
                </section>

                {/* Featured Collections */}
                {/* <section className="w-full">
                    <FeaturedCollections />
                </section> 
                */}

                {/* Best Sellers */}
                <section className="w-full">
                    <BestSellers />
                </section>

                {/* New Arrivals */}
                <section className="w-full">
                    <NewArrivals />
                </section>

                {/* Top Rated */}
                <section className="w-full">
                    <TopRated />
                </section>

                {/* Brand Carousel */}
                <section className="w-full">
                    <BrandCarousel />
                </section>

                {/* Customer Testimonials */}
                <section className="w-full">
                    <Testimonials />
                </section>
                {/* Newsletter Signup */}
                <section className="w-full">
                    <Newsletter />
                </section>
            </div>
        </StoreLayout>
    );
}
