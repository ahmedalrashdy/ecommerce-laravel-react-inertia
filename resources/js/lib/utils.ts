import { InertiaLinkProps } from '@inertiajs/react';
import { type ClassValue, clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function toUrl(url: NonNullable<InertiaLinkProps['href']>): string {
    return typeof url === 'string' ? url : url.url;
}

interface CurrencyOptions {
    locale?: string;
    currency?: string;
}

export function formatCurrency(
    amount: number | string,
    options: CurrencyOptions = {}
): string {
    const { locale = 'en-US', currency = 'USD' } = options;

    const rawAmount = typeof amount === 'string' ? amount.trim() : amount.toString();
    const sanitized = rawAmount.replace(/[$,\s]/g, '');

    if (!/^-?\d+(\.\d+)?$/.test(sanitized)) {
        console.error('Invalid number provided to formatCurrency:', amount);
        return '$0';
    }

    const isNegative = sanitized.startsWith('-');
    const unsigned = isNegative ? sanitized.slice(1) : sanitized;
    const [rawInteger, rawFraction = ''] = unsigned.split('.');
    const normalizedInteger = rawInteger.replace(/^0+(?=\d)/, '') || '0';
    const groupedInteger = new Intl.NumberFormat(locale, {
        useGrouping: true,
    }).format(BigInt(normalizedInteger));
    const normalizedFraction = rawFraction.slice(0, 2).padEnd(2, '0');
    const fraction = normalizedFraction === '00' ? '' : normalizedFraction;
    const symbol = currency === 'USD' ? '$' : currency;
    const sign = isNegative ? '-' : '';

    return `${sign}${symbol}${groupedInteger}${fraction ? `.${fraction}` : ''}`;
}

// const mediaBaseUrl = import.meta.env.VITE_MEDIA_BASE_URL ?? "https://pub-1c572fe32d4d4687ada9684c0015063b.r2.dev";
const mediaBaseUrl = "https://pub-1c572fe32d4d4687ada9684c0015063b.r2.dev";// ?? "https://pub-1c572fe32d4d4687ada9684c0015063b.r2.dev";

function isAbsoluteUrl(path: string): boolean {
    return /^(https?:)?\/\//i.test(path) || path.startsWith('data:') || path.startsWith('blob:');
}

function ensureLeadingSlash(path: string): string {
    return path.startsWith('/') ? path : `/${path}`;
}

function normalizeRelativePath(path: string): string {
    if (path.startsWith('/')) {
        return path;
    }

    if (path.startsWith('./')) {
        return ensureLeadingSlash(path.slice(2));
    }

    return ensureLeadingSlash(path);
}

function joinBaseUrl(baseUrl: string, path: string): string {
    const normalizedBase = baseUrl.endsWith('/') ? baseUrl.slice(0, -1) : baseUrl;
    const normalizedPath = ensureLeadingSlash(path);

    return `${normalizedBase}${normalizedPath}`;
}

export function storageUrl(path?: string | null): string {
    if (!path) {
        return '';
    }

    if (isAbsoluteUrl(path)) {
        return path;
    }

    const resolvedPath = normalizeRelativePath(path);

    if (mediaBaseUrl !== '') {
        return joinBaseUrl(mediaBaseUrl, resolvedPath);
    }

    return resolvedPath;
}
