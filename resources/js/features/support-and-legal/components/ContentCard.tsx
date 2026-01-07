import React, { ReactNode } from 'react';

interface ContentCardProps {
    /**
     * Card content
     */
    children: ReactNode;
    /**
     * Optional additional className
     */
    className?: string;
}

/**
 * Content Card Component
 * Card wrapper for main content areas
 */
export function ContentCard({ children, className = '' }: ContentCardProps) {
    return (
        <div
            className={`rounded-2xl border border-border bg-card p-6 shadow-sm ${className}`}
        >
            {children}
        </div>
    );
}
