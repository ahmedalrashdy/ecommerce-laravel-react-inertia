import type { UserAddress } from '@/types/address';

export function formatAddress(address: UserAddress): string {
    const parts: string[] = [];

    if (address.address_line_1) {
        parts.push(address.address_line_1);
    }

    if (address.address_line_2) {
        parts.push(address.address_line_2);
    }

    if (address.city) {
        parts.push(address.city);
    }

    if (address.state) {
        parts.push(address.state);
    }

    if (address.country) {
        parts.push(address.country);
    }

    if (address.postal_code) {
        parts.push(address.postal_code);
    }

    return parts.join(', ');
}
