import * as React from 'react';
import { Search, X, Package, Tag, Building2, Loader2 } from 'lucide-react';
import { cn, storageUrl } from '@/lib/utils';
import { router } from '@inertiajs/react';
import { index as productsIndex } from '@/routes/store/products';
import { suggestions as searchSuggestions } from '@/actions/App/Http/Controllers/Store/Search/SearchController';
import axios from 'axios';
import useClickOutside from '@/hooks/useClickOutSide';
import styles from './SearchBar.module.css';

interface SearchSuggestion {
    id: number;
    name: string;
    slug: string;
    type: 'product' | 'category' | 'brand';
    image: string | null;
    price: string | null;
}

interface SearchBarProps {
    className?: string;
    placeholder?: string;
}

const MIN_QUERY_LENGTH = 2;
const DEBOUNCE_DELAY = 300;

// عدّل هذا حسب مسار صفحة المنتج لديكم (إن كان مختلفًا)
const PRODUCT_SHOW_BASE_PATH = '/products';

function normalizeQuery(input: string): string {
    return input.trim().replace(/\s+/g, ' ');
}

function isMobileViewport(): boolean {
    return typeof window !== 'undefined' && window.matchMedia('(max-width: 640px)').matches;
}

type CachedEntry = {
    ts: number;
    suggestions: SearchSuggestion[];
};

const CACHE_TTL_MS = 30_000;

export const SearchBar: React.FC<SearchBarProps> = ({
    className = '',
    placeholder = 'ابحث عن منتجات، علامات تجارية، فئات...',
}) => {
    const [searchQuery, setSearchQuery] = React.useState('');
    const [suggestions, setSuggestions] = React.useState<SearchSuggestion[]>([]);
    const [isLoading, setIsLoading] = React.useState(false);
    const [isOpen, setIsOpen] = React.useState(false);
    const [selectedIndex, setSelectedIndex] = React.useState(-1);

    const containerRef = React.useRef<HTMLDivElement>(null);
    const inputRef = React.useRef<HTMLInputElement>(null);
    const abortRef = React.useRef<AbortController | null>(null);
    const debounceTimerRef = React.useRef<ReturnType<typeof setTimeout> | null>(null);
    const latestQueryRef = React.useRef<string>('');
    const cacheRef = React.useRef<Map<string, CachedEntry>>(new Map());

    useClickOutside(containerRef, () => {
        setIsOpen(false);
        setSelectedIndex(-1);
    });

    const cancelInFlight = React.useCallback((reason: string) => {
        if (abortRef.current) {
            abortRef.current.abort(reason);
            abortRef.current = null;
        }
    }, []);

    const fetchSuggestions = React.useCallback(
        async (rawQuery: string) => {
            const query = normalizeQuery(rawQuery);

            // عند قِصر الاستعلام: ألغِ الطلب الجاري + امسح النتائج
            if (query.length < MIN_QUERY_LENGTH) {
                cancelInFlight('Query too short');
                setIsLoading(false);
                setSuggestions([]);
                setIsOpen(false);
                setSelectedIndex(-1);
                return;
            }

            // cache hit
            const cached = cacheRef.current.get(query);
            if (cached && Date.now() - cached.ts <= CACHE_TTL_MS) {
                setSuggestions(cached.suggestions);
                setIsOpen(true);
                setIsLoading(false);
                setSelectedIndex(-1);
                return;
            }

            // cancel previous request
            cancelInFlight('New request initiated');

            const controller = new AbortController();
            abortRef.current = controller;

            latestQueryRef.current = query;
            setIsLoading(true);

            try {
                const response = await axios.get<{ suggestions: SearchSuggestion[] }>(
                    searchSuggestions.url(),
                    {
                        params: { q: query },
                        signal: controller.signal,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    },
                );

                // guard ضد الردود القديمة
                if (latestQueryRef.current !== query) return;

                const list = response.data.suggestions ?? [];
                cacheRef.current.set(query, { ts: Date.now(), suggestions: list });

                setSuggestions(list);
                setIsOpen(true); // افتح حتى لو لم توجد نتائج لعرض Empty State + زر عرض الكل
                setSelectedIndex(-1);
            } catch (error: any) {
                // تجاهل الإلغاء
                if (error?.name === 'CanceledError' || error?.code === 'ERR_CANCELED') return;

                console.error('Search error:', error);
                setSuggestions([]);
                setIsOpen(true); // افتح لإظهار empty/error state إن رغبت
                setSelectedIndex(-1);
            } finally {
                // لا تُطفئ loading لو تم استبدال الاستعلام أثناء الطلب
                if (latestQueryRef.current === query) {
                    setIsLoading(false);
                }
            }
        },
        [cancelInFlight],
    );

    const debouncedFetch = React.useCallback(
        (q: string) => {
            if (debounceTimerRef.current) {
                clearTimeout(debounceTimerRef.current);
            }
            debounceTimerRef.current = setTimeout(() => {
                void fetchSuggestions(q);
            }, DEBOUNCE_DELAY);
        },
        [fetchSuggestions],
    );

    React.useEffect(() => {
        debouncedFetch(searchQuery);

        return () => {
            if (debounceTimerRef.current) clearTimeout(debounceTimerRef.current);
        };
    }, [searchQuery, debouncedFetch]);

    React.useEffect(() => {
        return () => {
            if (debounceTimerRef.current) clearTimeout(debounceTimerRef.current);
            cancelInFlight('Component unmounted');
        };
    }, [cancelInFlight]);

    const navigateToProducts = (query: string) => {
        const q = normalizeQuery(query);
        if (!q) return;

        router.get(productsIndex.url(), { q });
        setIsOpen(false);
        setSelectedIndex(-1);
        inputRef.current?.blur();
    };

    const navigateBySuggestion = (suggestion: SearchSuggestion) => {
        setIsOpen(false);
        setSelectedIndex(-1);

        switch (suggestion.type) {
            case 'product':
                // إن كان مسار صفحة المنتج مختلفًا عدّل PRODUCT_SHOW_BASE_PATH
                router.get(`${PRODUCT_SHOW_BASE_PATH}/${suggestion.slug}`);
                break;
            case 'category':
                router.get(productsIndex.url(), { category: suggestion.slug });
                break;
            case 'brand':
                router.get(productsIndex.url(), { brand: suggestion.slug });
                break;
        }
    };

    const handleSearch = (e: React.FormEvent) => {
        e.preventDefault();
        navigateToProducts(searchQuery);
    };

    const groupedSuggestions = React.useMemo(() => {
        const groups: Record<SearchSuggestion['type'], SearchSuggestion[]> = {
            product: [],
            category: [],
            brand: [],
        };

        for (const s of suggestions) {
            groups[s.type].push(s);
        }

        return groups;
    }, [suggestions]);

    const flatSuggestions = React.useMemo(() => {
        return [
            ...groupedSuggestions.product,
            ...groupedSuggestions.category,
            ...groupedSuggestions.brand,
        ];
    }, [groupedSuggestions]);

    // تأكد أن selectedIndex لا يخرج عن النطاق عند تغيّر النتائج
    React.useEffect(() => {
        if (selectedIndex >= flatSuggestions.length) {
            setSelectedIndex(-1);
        }
    }, [flatSuggestions.length, selectedIndex]);

    const getSuggestionIndex = (type: SearchSuggestion['type'], indexInGroup: number) => {
        let offset = 0;
        if (type === 'category') offset = groupedSuggestions.product.length;
        if (type === 'brand') offset = groupedSuggestions.product.length + groupedSuggestions.category.length;
        return offset + indexInGroup;
    };

    const handleKeyDown = (e: React.KeyboardEvent<HTMLInputElement>) => {
        const q = normalizeQuery(searchQuery);

        // Enter بدون dropdown: نفّذ بحث عادي (مع منع submit المكرر)
        if (!isOpen || flatSuggestions.length === 0) {
            if (e.key === 'Enter') {
                e.preventDefault();
                navigateToProducts(q);
            } else if (e.key === 'Escape') {
                setIsOpen(false);
                setSelectedIndex(-1);
                inputRef.current?.blur();
            }
            return;
        }

        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                setSelectedIndex((prev) => (prev < flatSuggestions.length - 1 ? prev + 1 : 0));
                break;

            case 'ArrowUp':
                e.preventDefault();
                setSelectedIndex((prev) => (prev > 0 ? prev - 1 : flatSuggestions.length - 1));
                break;

            case 'Enter':
                e.preventDefault();
                if (selectedIndex >= 0 && flatSuggestions[selectedIndex]) {
                    navigateBySuggestion(flatSuggestions[selectedIndex]);
                } else {
                    navigateToProducts(q);
                }
                break;

            case 'Escape':
                setIsOpen(false);
                setSelectedIndex(-1);
                inputRef.current?.blur();
                break;
        }
    };

    const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const next = e.target.value;
        setSearchQuery(next);

        const q = normalizeQuery(next);
        if (q.length < MIN_QUERY_LENGTH) {
            // مهم: إلغاء الطلب الجاري + إغلاق
            cancelInFlight('Query too short (input change)');
            setSuggestions([]);
            setIsLoading(false);
            setIsOpen(false);
            setSelectedIndex(-1);
        } else {
            // افتح لعرض loading/empty/results
            setIsOpen(true);
        }
    };

    const handleClear = () => {
        cancelInFlight('Clear clicked');
        setSearchQuery('');
        setSuggestions([]);
        setIsLoading(false);
        setIsOpen(false);
        setSelectedIndex(-1);
        inputRef.current?.focus();
    };

    const getTypeIcon = (type: SearchSuggestion['type']) => {
        switch (type) {
            case 'product':
                return <Package className="h-4 w-4" />;
            case 'category':
                return <Tag className="h-4 w-4" />;
            case 'brand':
                return <Building2 className="h-4 w-4" />;
        }
    };

    const getTypeLabel = (type: SearchSuggestion['type']) => {
        switch (type) {
            case 'product':
                return 'منتجات';
            case 'category':
                return 'فئات';
            case 'brand':
                return 'علامات تجارية';
        }
    };

    const qNormalized = normalizeQuery(searchQuery);
    const shouldShowDropdown = isOpen && qNormalized.length >= MIN_QUERY_LENGTH;

    // تحسين موضع dropdown على الجوال (تجنب fixed + top-auto غير المحدد)
    const dropdownStyle = React.useMemo<React.CSSProperties | undefined>(() => {
        if (!shouldShowDropdown) return undefined;
        if (!isMobileViewport()) return undefined;

        const el = containerRef.current;
        if (!el) return undefined;

        const rect = el.getBoundingClientRect();
        const top = rect.bottom + 8;

        return {
            position: 'fixed',
            left: 16,
            right: 16,
            top,
            maxHeight: '50vh',
        };
    }, [shouldShowDropdown]);

    return (
        <div ref={containerRef} className={cn(styles.container, className)}>
            <form onSubmit={handleSearch} className={styles.form}>
                <div className={styles.inputWrapper}>
                    <input
                        ref={inputRef}
                        type="text"
                        value={searchQuery}
                        onChange={handleInputChange}
                        onKeyDown={handleKeyDown}
                        onFocus={() => {
                            const q = normalizeQuery(searchQuery);
                            if (q.length >= MIN_QUERY_LENGTH) {
                                setIsOpen(true);
                            }
                        }}
                        placeholder={placeholder}
                        className={styles.input}
                        autoComplete="off"
                        aria-label="بحث"
                        aria-expanded={shouldShowDropdown}
                        aria-controls="search-suggestions"
                        aria-activedescendant={selectedIndex >= 0 ? `suggestion-${selectedIndex}` : undefined}
                        role="combobox"
                    />

                    <button type="submit" className={styles.submitButton} aria-label="بحث">
                        {isLoading ? (
                            <Loader2 className={cn(styles.submitIcon, 'animate-spin')} />
                        ) : (
                            <Search className={styles.submitIcon} />
                        )}
                    </button>

                    {searchQuery && (
                        <button
                            type="button"
                            onClick={handleClear}
                            className={styles.clearButton}
                            aria-label="مسح البحث"
                        >
                            <X className={styles.clearIcon} />
                        </button>
                    )}
                </div>
            </form>

            {shouldShowDropdown && (
                <div
                    id="search-suggestions"
                    className={styles.suggestionsDropdown}
                    style={dropdownStyle}
                    role="listbox"
                >
                    {isLoading && (
                        <div className={styles.loadingRow}>
                            <Loader2 className={cn(styles.loadingIcon, 'animate-spin')} />
                            <span>جارٍ البحث…</span>
                        </div>
                    )}

                    {!isLoading && flatSuggestions.length === 0 && (
                        <div className={styles.emptyState}>
                            لا توجد نتائج مطابقة.
                        </div>
                    )}

                    {Object.entries(groupedSuggestions).map(([type, items]) => {
                        if (items.length === 0) return null;

                        const typed = type as SearchSuggestion['type'];

                        return (
                            <div key={type} className={styles.suggestionGroup}>
                                <div className={styles.groupHeader}>
                                    {getTypeIcon(typed)}
                                    <span>{getTypeLabel(typed)}</span>
                                </div>

                                {items.map((suggestion, indexInGroup) => {
                                    const globalIndex = getSuggestionIndex(typed, indexInGroup);

                                    return (
                                        <button
                                            key={`${type}-${suggestion.id}`}
                                            id={`suggestion-${globalIndex}`}
                                            type="button"
                                            className={cn(
                                                styles.suggestionItem,
                                                selectedIndex === globalIndex && styles.suggestionItemSelected,
                                            )}
                                            onClick={() => navigateBySuggestion(suggestion)}
                                            onMouseEnter={() => setSelectedIndex(globalIndex)}
                                            role="option"
                                            aria-selected={selectedIndex === globalIndex}
                                        >
                                            {suggestion.image ? (
                                                <img
                                                    src={storageUrl(suggestion.image)}
                                                    alt={suggestion.name}
                                                    className={styles.suggestionImage}
                                                    loading="lazy"
                                                />
                                            ) : (
                                                <div className={styles.suggestionImagePlaceholder}>
                                                    {getTypeIcon(suggestion.type)}
                                                </div>
                                            )}

                                            <div className={styles.suggestionContent}>
                                                <span className={styles.suggestionName}>{suggestion.name}</span>
                                                {suggestion.price && (
                                                    <span className={styles.suggestionPrice}>
                                                        {suggestion.price} ر.س
                                                    </span>
                                                )}
                                            </div>
                                        </button>
                                    );
                                })}
                            </div>
                        );
                    })}

                    <button
                        type="button"
                        className={styles.viewAllButton}
                        onClick={() => navigateToProducts(qNormalized)}
                    >
                        <Search className="h-4 w-4" />
                        <span>عرض كل النتائج لـ "{qNormalized}"</span>
                    </button>
                </div>
            )}
        </div>
    );
};

export default SearchBar;
