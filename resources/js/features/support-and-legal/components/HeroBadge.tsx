import { Link } from '@inertiajs/react';
import React, { ReactNode } from 'react';

interface HeroBadgeProps {
    /**
     * Content to display inside the badge
     */
    children: ReactNode;
    /**
     * Optional link href (internal or external)
     */
    href?: string;
    /**
     * Whether this is an external link
     */
    external?: boolean;
}

/**
 * Hero Badge Component
 * Small badge/pill component for displaying info or links in the hero section
 */
export function HeroBadge({ children, href, external }: HeroBadgeProps) {
    const className =
        'rounded-full bg-white/15 px-4 py-2 text-sm font-medium text-white ring-1 ring-white/30 backdrop-blur-md transition-all hover:scale-105 hover:bg-white/25';

    if (!href) {
        return <span className={className}>{children}</span>;
    }

    if (external || href.startsWith('http')) {
        return (
            <a
                href={href}
                target="_blank"
                rel="noopener noreferrer"
                className={className}
            >
                {children}
            </a>
        );
    }

    return (
        <Link href={href} className={className}>
            {children}
        </Link>
    );
}
