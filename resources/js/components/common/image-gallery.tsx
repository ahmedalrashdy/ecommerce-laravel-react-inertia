import { cn, storageUrl } from '@/lib/utils';
import { ChevronLeft, ChevronRight } from 'lucide-react';
import React from 'react';

export const ImageGallery: React.FC<{ images: App.Data.Basic.ImageData[] }> = ({
    images,
}) => {
    // Fallback logic
    const displayImages = React.useMemo(
        () =>
            images && images.length > 0
                ? images
                : [
                      {
                          id: 0,
                          path: 'https://placehold.co/600x400/png?text=No+Image',
                          altText: 'No image',
                          displayOrder: 1,
                      },
                  ],
        [images],
    );

    const [selectedIndex, setSelectedIndex] = React.useState(0);
    return (
        <div
            className="flex w-full flex-col-reverse gap-3 md:flex-row"
            dir="ltr"
        >
            {/* Thumbnails (Vertical strip on desktop, Horizontal on mobile) */}
            {displayImages.length > 1 && (
                <div className="scrollbar-hide flex justify-center gap-2 overflow-x-auto px-1 py-1 md:max-h-[450px] md:flex-col md:justify-start md:overflow-y-auto">
                    {displayImages.map((image, index) => (
                        <button
                            key={image.path}
                            onClick={() => setSelectedIndex(index)}
                            className={cn(
                                'relative h-14 w-14 shrink-0 overflow-hidden rounded-lg border transition-all duration-200 md:h-16 md:w-16',
                                selectedIndex === index
                                    ? 'border-primary opacity-100 ring-1 ring-primary/30'
                                    : 'border-transparent opacity-60 hover:border-border hover:opacity-100',
                            )}
                        >
                            <img
                                src={storageUrl(image.path)}
                                alt={image.altText ?? 'thumbnail'}
                                className="h-full w-full object-cover"
                            />
                        </button>
                    ))}
                </div>
            )}

            {/* Main Image Stage - Fixed Height Constraint */}
            <div className="relative flex-1 overflow-hidden rounded-xl border border-border/50 bg-white dark:bg-muted">
                <div className="relative flex h-[300px] w-full items-center justify-center bg-gray-50 sm:h-[400px] md:h-[450px] dark:bg-zinc-900/50">
                    <img
                        src={storageUrl(displayImages[selectedIndex].path)}
                        alt={
                            displayImages[selectedIndex]?.altText ??
                            'product-image'
                        }
                        className="max-h-full max-w-full object-contain p-2 transition-opacity duration-300"
                    />
                </div>

                {/* Arrows overlay if multiple images */}
                {displayImages.length > 1 && (
                    <>
                        <button
                            onClick={() =>
                                setSelectedIndex(
                                    (i) =>
                                        (i - 1 + displayImages.length) %
                                        displayImages.length,
                                )
                            }
                            className="absolute top-1/2 left-2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-black shadow-sm transition-all hover:bg-white"
                        >
                            <ChevronLeft className="h-4 w-4" />
                        </button>
                        <button
                            onClick={() =>
                                setSelectedIndex(
                                    (i) => (i + 1) % displayImages.length,
                                )
                            }
                            className="absolute top-1/2 right-2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-black shadow-sm transition-all hover:bg-white"
                        >
                            <ChevronRight className="h-4 w-4" />
                        </button>
                    </>
                )}
            </div>
        </div>
    );
};
