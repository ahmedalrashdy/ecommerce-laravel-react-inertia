import { LucideIcon } from 'lucide-react';
import React from 'react';

interface ContactBadgeProps {
    /**
     * Icon component
     */
    icon: LucideIcon;
    /**
     * Link href
     */
    href: string;
    /**
     * Display text
     */
    children: React.ReactNode;
    /**
     * Whether this is an external link
     */
    external?: boolean;
}

/**
 * Contact Badge Component
 * Small badge with icon for contact information in footer CTAs
 */
export function ContactBadge({
    icon: Icon,
    href,
    children,
    external = false,
}: ContactBadgeProps) {
    const props = external
        ? { target: '_blank', rel: 'noopener noreferrer' }
        : {};

    return (
        <a
            href={href}
            {...props}
            className="inline-flex items-center gap-2 rounded-full bg-primary-foreground/10 px-5 py-2.5 text-sm font-medium text-primary-foreground transition-all hover:bg-primary-foreground/20"
        >
            <Icon className="size-4" />
            <span>{children}</span>
        </a>
    );
}
