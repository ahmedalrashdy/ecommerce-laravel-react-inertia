import React, { ReactNode } from 'react';

interface TableOfContentsSection {
    /**
     * Section ID for anchor link
     */
    id: string;
    /**
     * Section title to display
     */
    title: string;
}

interface TableOfContentsProps {
    /**
     * List of sections to display
     */
    sections: TableOfContentsSection[];
    /**
     * Optional title for the TOC
     */
    title?: string;
    /**
     * Optional additional content/info box
     */
    footer?: ReactNode;
}

/**
 * Table of Contents Component
 * Sticky sidebar navigation for long-form content pages
 */
export function TableOfContents({
    sections,
    title = 'المحتويات',
    footer,
}: TableOfContentsProps) {
    return (
        <div className="sticky top-6 rounded-2xl border border-border bg-card p-5 shadow-sm">
            <p className="text-sm font-bold text-foreground">{title}</p>
            <nav className="mt-4 space-y-1">
                {sections.map((section) => (
                    <a
                        key={section.id}
                        href={`#${section.id}`}
                        className="block rounded-xl px-3 py-2 text-sm text-muted-foreground transition-all hover:bg-muted/50 hover:text-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                    >
                        {section.title}
                    </a>
                ))}
            </nav>
            {footer && <div className="mt-5">{footer}</div>}
        </div>
    );
}
