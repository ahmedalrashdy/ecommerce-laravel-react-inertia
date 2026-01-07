import { LucideIcon } from 'lucide-react';
import { ReactNode } from 'react';

/**
 * Common Types for Support and Legal Components
 */

export interface Section {
    id: string;
    title: string;
    body?: ReactNode;
}

export interface QuickLink {
    title: string;
    href: string;
}

export interface ContactCard {
    title: string;
    desc: string;
    value: string;
    href: string;
    icon: LucideIcon;
    external?: boolean;
}

export interface FAQ {
    id?: string;
    q: string;
    a: string;
    category?: string;
}
