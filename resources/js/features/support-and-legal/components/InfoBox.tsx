import React, { ReactNode } from 'react';

interface InfoBoxProps {
    /**
     * InfoBox content
     */
    children: ReactNode;
    /**
     * Optional additional className
     */
    className?: string;
}

/**
 * Info Box Component
 * Highlighted box for additional information or notes
 */
export function InfoBox({ children, className = '' }: InfoBoxProps) {
    return (
        <div
            className={`rounded-xl bg-muted/50 p-4 ring-1 ring-border ${className}`}
        >
            {children}
        </div>
    );
}
