import { LucideIcon } from 'lucide-react';
import React, { ReactNode } from 'react';

import { BrandMark } from './BrandMark';

interface HeroSectionProps {
    /**
     * Icon to display in the brand mark
     */
    icon: LucideIcon;
    /**
     * Arabic brand name
     */
    nameAr: string;
    /**
     * English brand name
     */
    nameEn: string;
    /**
     * Main page title
     */
    title: string;
    /**
     * Description or subtitle text
     */
    description: string;
    /**
     * Background image URL
     */
    backgroundImage: string;
    /**
     * Optional action buttons or elements
     */
    actions?: ReactNode;
    /**
     * Optional badges or quick links
     */
    badges?: ReactNode;
}

/**
 * Hero Section Component
 * Reusable hero section for support and legal pages
 */
export function HeroSection({
    icon,
    nameAr,
    nameEn,
    title,
    description,
    backgroundImage,
    actions,
    badges,
}: HeroSectionProps) {
    return (
        <section className="relative overflow-hidden">
            <div className="absolute inset-0 z-0">
                <img
                    src={backgroundImage}
                    alt={`خلفية ${title}`}
                    className="h-full w-full object-cover"
                    loading="eager"
                />
                <div className="absolute inset-0 bg-linear-to-b from-black/70 via-black/50 to-transparent" />
            </div>

            <div className="container relative z-10 mx-auto px-4 sm:px-6 lg:px-8">
                <div className="py-16 sm:py-24 lg:py-32">
                    <div className="max-w-2xl">
                        <BrandMark
                            icon={icon}
                            nameAr={nameAr}
                            nameEn={nameEn}
                        />

                        <h1 className="mt-8 text-4xl font-bold tracking-tight text-white sm:text-5xl lg:text-6xl">
                            {title}
                        </h1>

                        <p className="mt-6 text-lg leading-8 text-white/95 sm:text-xl">
                            {description}
                        </p>

                        {actions && (
                            <div className="mt-8 flex flex-wrap gap-4">
                                {actions}
                            </div>
                        )}

                        {badges && (
                            <div className="mt-10 flex flex-wrap gap-3">
                                {badges}
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </section>
    );
}
