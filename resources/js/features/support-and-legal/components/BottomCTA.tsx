import React, { ReactNode } from 'react';

interface BottomCTAProps {
    /**
     * Title text
     */
    title: string;
    /**
     * Description text
     */
    description: string;
    /**
     * Action buttons or elements
     */
    actions?: ReactNode;
}

/**
 * Bottom CTA Component
 * Call-to-action section typically shown at the bottom of content
 */
export function BottomCTA({ title, description, actions }: BottomCTAProps) {
    return (
        <div className="mt-6 rounded-xl bg-muted/50 p-6 ring-1 ring-border">
            <div className="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p className="text-sm font-bold text-foreground">{title}</p>
                    <p className="mt-1 text-sm text-muted-foreground">
                        {description}
                    </p>
                </div>
                {actions && (
                    <div className="flex flex-wrap gap-2">{actions}</div>
                )}
            </div>
        </div>
    );
}
