import { Link } from "@inertiajs/react"
import { ArrowLeft } from "lucide-react"
import { Button } from "@/components/ui/button"
import { styles } from './FeaturedCollections.styles'

interface Collection {
    id: number
    title: string
    description: string
    image: string
    link: string
    badge?: string
}

const collections: Collection[] = [
    {
        id: 1,
        title: "مجموعة الشتاء",
        description: "اكتشف أحدث صيحات الموضة الشتوية مع خصومات تصل إلى 40%",
        image: "https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=1200&q=80",
        link: "/collections/winter",
        badge: "وصل حديثاً",
    },
    {
        id: 2,
        title: "تقنية المستقبل",
        description: "أحدث الأجهزة الذكية والتقنية المتطورة بأسعار لا تقاوم",
        image: "https://images.unsplash.com/photo-1550009158-9ebf69173e03?auto=format&fit=crop&w=1200&q=80",
        link: "/collections/tech",
        badge: "الأكثر مبيعاً",
    },
]

const FeaturedCollections: React.FC = () => {
    return (
        <section className={styles.section}>
            <div className={styles.container}>
                {/* Section Header */}
                <div className={styles.header}>
                    <h2 className={styles.title}>المجموعات المميزة</h2>
                    <p className={styles.description}>
                        تشكيلات مختارة بعناية من أفضل منتجاتنا
                    </p>
                </div>

                {/* Collections Grid */}
                <div className={styles.grid}>
                    {collections.map((collection) => (
                        <CollectionCard key={collection.id} collection={collection} />
                    ))}
                </div>
            </div>
        </section>
    )
}

const CollectionCard: React.FC<{ collection: Collection }> = ({ collection }) => {
    return (
        <Link
            href={collection.link}
            className={styles.card}
        >
            <div className={styles.imageContainer}>
                {/* Background Image */}
                <img
                    src={collection.image}
                    alt={collection.title}
                    className={styles.image}
                />

                {/* Gradient Overlay */}
                <div className={styles.gradientOverlay} />

                {/* Badge */}
                {collection.badge && (
                    <div className={styles.badge}>
                        {collection.badge}
                    </div>
                )}

                {/* Content */}
                <div className={styles.content}>
                    <h3 className={styles.contentTitle}>{collection.title}</h3>
                    <p className={styles.contentDescription}>{collection.description}</p>

                    <Button
                        size="lg"
                        className={styles.actionButton}
                        onClick={(e) => e.preventDefault()}
                    >
                        اكتشف المجموعة
                        <ArrowLeft className={styles.actionButtonIcon} />
                    </Button>
                </div>
            </div>
        </Link>
    )
}

export default FeaturedCollections;
