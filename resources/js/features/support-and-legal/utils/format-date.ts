/**
 * Format ISO date string to Arabic date format
 * @param iso - ISO date string (YYYY-MM-DD)
 * @returns Formatted date string (YYYY/MM/DD)
 */
export function formatDate(iso: string): string {
    const [y, m, d] = String(iso || '').split('-');
    if (!y || !m || !d) {
        return iso;
    }
    return `${y}/${m}/${d}`;
}
