import React, { ReactNode } from 'react';

interface PageLayoutProps {
    /**
     * Sidebar content (typically TableOfContents)
     */
    sidebar?: ReactNode;
    /**
     * Main content area
     */
    children: ReactNode;
    /**
     * Whether to apply negative margin top (for elevated design)
     */
    elevated?: boolean;
}

/**
 * Page Layout Component
 * Two-column layout with optional sidebar for support and legal pages
 */
export function PageLayout({
    sidebar,
    children,
    elevated = true,
}: PageLayoutProps) {
    return (
        <section
            className={`container mx-auto px-4 py-10 sm:px-6 sm:py-14 lg:px-8 ${
                elevated ? 'relative z-10 -mt-8' : ''
            }`}
        >
            <div className="grid gap-8 lg:grid-cols-12 lg:items-start">
                {sidebar && (
                    <aside className="lg:col-span-4">{sidebar}</aside>
                )}
                <div className={sidebar ? 'lg:col-span-8' : 'lg:col-span-12'}>
                    {children}
                </div>
            </div>
        </section>
    );
}
