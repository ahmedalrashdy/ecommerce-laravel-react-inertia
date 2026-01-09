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

export function storageUrl(path?: string | null): string {
    if (!path) {
        return '';
    }

    if (/^(https?:)?\/\//i.test(path) || path.startsWith('data:')) {
        return path;
    }

    if (path.startsWith('/')) {
        return path;
    }

    return `/storage/${path}`;
}
