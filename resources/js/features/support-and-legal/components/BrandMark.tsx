import { LucideIcon } from 'lucide-react';
import React from 'react';

interface BrandMarkProps {
    /**
     * The icon component to display
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
}

/**
 * Brand Mark Component
 * Displays the brand icon and names in a consistent format
 */
export function BrandMark({ icon: Icon, nameAr, nameEn }: BrandMarkProps) {
    return (
        <div className="inline-flex items-center gap-3">
            <div className="grid size-12 place-items-center rounded-2xl bg-white/15 ring-2 ring-white/30 backdrop-blur-md transition-all hover:scale-110 hover:bg-white/20">
                <Icon className="size-6 text-white" />
            </div>
            <div className="leading-tight">
                <p className="text-sm font-bold text-white">{nameAr}</p>
                <p className="text-xs font-medium text-white/90">{nameEn}</p>
            </div>
        </div>
    );
}
