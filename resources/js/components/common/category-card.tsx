import { storageUrl } from '@/lib/utils';
import { show as categoryShow } from '@/routes/store/category';
import { Link } from '@inertiajs/react';

export const CategoryCard: React.FC<{
    category: App.Data.Basic.CategoryData;
}> = ({ category }) => {
    return (
        <Link
            href={categoryShow.url(category.slug)}
            className="group relative block w-[160px] flex-shrink-0 snap-start overflow-hidden rounded-2xl border border-border/50 bg-card transition-all duration-300 hover:-translate-y-1 hover:shadow-lg md:w-[200px]"
        >
            {/* Category Image */}
            <div className="relative h-[200px] overflow-hidden md:h-[240px]">
                <img
                    {...(category.image && { src: storageUrl(category.image) })}
                    alt={category.name}
                    className="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                />

                {/* Gradient Overlay */}
                <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-80 transition-opacity group-hover:opacity-90" />

                {/* Featured Badge */}
                {/* <div className="absolute top-3 right-3 rounded-full bg-primary/90 px-2.5 py-1 text-[10px] font-bold text-white shadow-sm backdrop-blur-sm">
                    مميز
                </div> 
                */}

                {/* Category Info */}
                <div className="absolute right-0 bottom-0 left-0 translate-y-2 transform p-4 text-white transition-transform duration-300 group-hover:translate-y-0">
                    <h3 className="mb-1 text-lg leading-tight font-bold">
                        {category.name}
                    </h3>
                </div>
            </div>
        </Link>
    );
};
