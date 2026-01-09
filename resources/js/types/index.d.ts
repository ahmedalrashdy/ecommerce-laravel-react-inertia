import { InertiaLinkProps } from '@inertiajs/react';
import { LucideIcon } from 'lucide-react';

export interface Auth {
    user: User|null;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavGroup {
    title: string;
    items: NavItem[];
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon | null;
    isActive?: boolean;
}

export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
    [key: string]: unknown;
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    two_factor_enabled?: boolean;
    created_at: string;
    updated_at: string;
    [key: string]: unknown; // This allows for additional properties...
}

export interface HeroSlide {
    id: number
    title: string
    subtitle: string
    description: string
    primaryCTA: { text: string; href: string }
    secondaryCTA?: { text: string; href: string }
    image: string
    badge?: { text: string; icon: "sparkles" | "flame" | "star" | "zap" }
    color: string
}

export interface Review {
    id: number
    user: { name: string; avatar: string }
    rating: number
    comment: string
    date: string
    helpful: number
    notHelpful: number
    images?: string[]
    verified: boolean
}

export interface PaginatedResponse<T> {
    current_page: number;
    data: T[];
  
    first_page_url: string | null;
    from: number | null;
  
    last_page: number;
    last_page_url: string | null;
  
    links: PaginationLink[];
  
    next_page_url: string | null;
    prev_page_url: string | null;
  
    path: string;
    per_page: number;
  
    to: number | null;
    total: number;
  }
  
  export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
  }
  