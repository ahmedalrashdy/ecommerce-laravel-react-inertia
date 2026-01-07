import { Circle } from 'lucide-react';
import React from 'react';

interface ContentBulletProps {
    /**
     * Bullet point title
     */
    title: string;
    /**
     * Bullet point description
     */
    desc: string;
}

/**
 * Content Bullet Component
 * Displays a bullet point with icon, title, and description
 */
export function ContentBullet({ title, desc }: ContentBulletProps) {
    return (
        <div className="flex items-start gap-3 rounded-xl bg-muted/50 p-4 ring-1 ring-border">
            <span className="mt-0.5 grid size-9 shrink-0 place-items-center rounded-lg bg-primary text-primary-foreground">
                <Circle className="size-4" />
            </span>
            <div className="min-w-0 flex-1">
                <p className="text-sm font-bold text-foreground">{title}</p>
                <p className="mt-1 text-sm leading-7 text-muted-foreground">
                    {desc}
                </p>
            </div>
        </div>
    );
}
