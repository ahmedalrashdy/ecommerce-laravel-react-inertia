import React, { ReactNode } from 'react';

interface FooterCTAProps {
    /**
     * Main title
     */
    title: string;
    /**
     * Description text
     */
    description: string;
    /**
     * Brand name
     */
    brandName: string;
    /**
     * Primary action buttons
     */
    primaryActions?: ReactNode;
    /**
     * Secondary contact links/badges
     */
    contactBadges?: ReactNode;
    /**
     * Background color variant
     */
    variant?: 'primary' | 'secondary';
}

/**
 * Footer CTA Section Component
 * Large call-to-action section typically shown at the page bottom
 */
export function FooterCTA({
    title,
    description,
    brandName,
    primaryActions,
    contactBadges,
    variant = 'primary',
}: FooterCTAProps) {
    const bgClass = variant === 'primary' ? 'bg-primary' : 'bg-secondary';

    return (
        <section className={`${bgClass} py-16 sm:py-20`}>
            <div className="container mx-auto px-4 sm:px-6 lg:px-8">
                <div className="mx-auto max-w-4xl">
                    <div className="rounded-2xl border border-primary-foreground/10 bg-primary-foreground/5 p-8 sm:p-10 lg:p-12">
                        <div className="mb-8 text-center">
                            <h2 className="text-3xl font-bold text-primary-foreground sm:text-4xl">
                                {title}
                            </h2>
                            <p className="mt-4 text-lg text-primary-foreground/90">
                                {description}
                            </p>
                        </div>

                        {primaryActions && (
                            <div className="mb-8 flex flex-col items-center gap-6 sm:flex-row sm:justify-center">
                                {primaryActions}
                            </div>
                        )}

                        {contactBadges && (
                            <div className="flex flex-col items-center gap-4 border-t border-primary-foreground/10 pt-6 sm:flex-row sm:justify-center">
                                {contactBadges}
                            </div>
                        )}
                    </div>

                    <p className="mt-8 text-center text-sm text-primary-foreground/70">
                        © {new Date().getFullYear()} {brandName}. جميع الحقوق
                        محفوظة.
                    </p>
                </div>
            </div>
        </section>
    );
}
