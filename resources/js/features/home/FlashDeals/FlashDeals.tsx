import * as React from "react"
import { Link } from "@inertiajs/react"
import { Clock, Flame, ArrowLeft, Heart, ShoppingCart } from "lucide-react"
import { Button } from "@/components/ui/button"
import { cn } from "@/lib/utils"
import { styles } from './FlashDeals.styles'

interface Product {
    id: number
    name: string
    image: string
    price: number
    originalPrice: number
    discount: number
    sold: number
    stock: number
}

const flashDeals: Product[] = [
    {
        id: 1,
        name: "هاتف ذكي بشاشة AMOLED",
        image: "https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=500&q=80",
        price: 1499,
        originalPrice: 2499,
        discount: 40,
        sold: 45,
        stock: 100,
    },
    {
        id: 2,
        name: "ساعة ذكية رياضية",
        image: "https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=500&q=80",
        price: 299,
        originalPrice: 599,
        discount: 50,
        sold: 78,
        stock: 100,
    },
    {
        id: 3,
        name: "سماعات لاسلكية عالية الجودة",
        image: "https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=500&q=80",
        price: 199,
        originalPrice: 399,
        discount: 50,
        sold: 92,
        stock: 100,
    },
    {
        id: 4,
        name: "كاميرا رقمية احترافية",
        image: "https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?auto=format&fit=crop&w=500&q=80",
        price: 2999,
        originalPrice: 4999,
        discount: 40,
        sold: 23,
        stock: 50,
    },
    {
        id: 5,
        name: "لابتوب ألعاب قوي",
        image: "https://images.unsplash.com/photo-1603302576837-37561b2e2302?auto=format&fit=crop&w=500&q=80",
        price: 4499,
        originalPrice: 6999,
        discount: 36,
        sold: 18,
        stock: 30,
    },
]

 const  FlashDeals: React.FC = () => {

    const [timeLeft, setTimeLeft] = React.useState({
        hours: 5,
        minutes: 32,
        seconds: 45,
    })

    React.useEffect(() => {
        const timer = setInterval(() => {
            setTimeLeft((prev) => {
                if (prev.seconds > 0) {
                    return { ...prev, seconds: prev.seconds - 1 }
                } else if (prev.minutes > 0) {
                    return { ...prev, minutes: prev.minutes - 1, seconds: 59 }
                } else if (prev.hours > 0) {
                    return { hours: prev.hours - 1, minutes: 59, seconds: 59 }
                }
                return prev
            })
        }, 1000)

        return () => clearInterval(timer)
    }, [])

    return (
        <section className={styles.section}>
            <div className={styles.container}>
                {/* Section Header */}
                <div className={styles.header}>
                    <div>
                        <div className={styles.headerContent}>
                            <div className={styles.iconContainer}>
                                <Flame className={styles.icon} />
                            </div>
                            <h2 className={styles.title}>عروض فلاش</h2>
                        </div>
                        <p className={styles.subtitle}>خصومات ضخمة لفترة محدودة جداً</p>
                    </div>

                    {/* Countdown Timer */}
                    <div className={styles.timerContainer}>
                        <Clock className={styles.timerIcon} />
                        <span className={styles.timerLabel}>ينتهي خلال:</span>
                        <div className={styles.timerBlocks}>
                            <TimeBlock value={timeLeft.hours} label="ساعة" />
                            <span className={styles.timerSeparator}>:</span>
                            <TimeBlock value={timeLeft.minutes} label="دقيقة" />
                            <span className={styles.timerSeparator}>:</span>
                            <TimeBlock value={timeLeft.seconds} label="ثانية" />
                        </div>
                    </div>
                </div>

                {/* Products Horizontal Scroll */}
                <div className={styles.productsContainer}>
                    <div className={styles.productsScroll}>
                        {flashDeals.map((product) => (
                            <FlashDealCard key={product.id} product={product} />
                        ))}
                    </div>
                </div>
                {/* View All Button */}
                <div className={styles.viewAllContainer}>
                    <Link href="#">
                        <Button size="lg" variant="outline" className={styles.viewAllButton}>
                            عرض جميع العروض
                            <ArrowLeft className={styles.viewAllIcon} />
                        </Button>
                    </Link>
                </div>
            </div>
        </section>
    )
}

const TimeBlock: React.FC<{ value: number; label: string }> = ({ value, label }) => (
    <div className={styles.timeBlock}>
        <div className={styles.timeBlockValue}>
            {String(value).padStart(2, "0")}
        </div>
        <span className={styles.timeBlockLabel}>{label}</span>
    </div>
)

const FlashDealCard: React.FC<{ product: Product }> = ({ product }) => {
    const progressPercent = (product.sold / product.stock) * 100

    return (
        <div className={styles.card}>
            {/* Product Image */}
            <div className={styles.imageContainer}>
                <Link href={`/products/${product.id}`}>
                    <img
                        src={product.image}
                        alt={product.name}
                        className={styles.image}
                           loading="lazy"
                    decoding="async"
                    />
                </Link>

                {/* Discount Badge */}
                <div className={styles.discountBadge}>
                    خصم {product.discount}%
                </div>

                {/* Quick Actions */}
                <div className={styles.quickActions}>
                    <Button size="icon" variant="secondary" className={styles.quickActionButton}>
                        <Heart className={styles.quickActionIcon} />
                    </Button>
                </div>
            </div>

            {/* Product Info */}
            <div className={styles.productInfo}>
                <Link href={`/products/${product.id}`}>
                    <h3 className={styles.productName}>
                        {product.name}
                    </h3>
                </Link>

                {/* Price */}
                <div className={styles.priceContainer}>
                    <span className={styles.price}>{product.price} ر.س</span>
                    <span className={styles.originalPrice}>{product.originalPrice} ر.س</span>
                </div>

                {/* Stock Progress */}
                <div className={styles.stockContainer}>
                    <div className={styles.stockInfo}>
                        <span>تم بيع {product.sold}</span>
                        <span>{product.stock - product.sold} متبقي</span>
                    </div>
                    <div className={styles.progressBar}>
                        <div
                            className={cn(
                                styles.progressBarFill,
                                progressPercent > 70 ? styles.progressBarHigh : progressPercent > 40 ? styles.progressBarMedium : styles.progressBarLow
                            )}
                            style={{ width: `${progressPercent}%` }}
                        />
                    </div>
                </div>

                {/* Add to Cart Button */}
                <Button className={styles.addToCartButton} size="sm">
                    <ShoppingCart className={styles.addToCartIcon} />
                    أضف للسلة
                </Button>
            </div>
        </div>
    )
}

export default FlashDeals;
