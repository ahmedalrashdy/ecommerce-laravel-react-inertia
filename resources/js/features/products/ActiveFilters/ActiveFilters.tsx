import * as React from "react"
import { X } from "lucide-react"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"

interface ActiveFiltersProps {
    selectedCategories: string[]
    onCategoryClear: () => void
    selectedBrands: string[]
    onBrandClear: () => void
    searchQuery?: string | null
    onSearchClear?: () => void
    onClearAll: () => void
    priceRange?: [number, number]
    maxPrice?: number
    currentCategorySlug?: string | null
    currentBrandSlug?: string | null
    categories?:App.Data.Basic.CategoryData[]
    brands?: App.Data.Basic.BrandData[]
}

export function ActiveFilters({
    selectedCategories,
    onCategoryClear,
    selectedBrands,
    onBrandClear,
    searchQuery,
    onSearchClear,
    onClearAll,
    priceRange,
    maxPrice = 5000,
    currentCategorySlug,
    currentBrandSlug,
    categories = [],
    brands = [],
}: ActiveFiltersProps) {
    const hasPriceFilter = priceRange && (priceRange[0] > 0 || priceRange[1] < maxPrice)
    const hasSearchFilter = !!searchQuery
    const activeFiltersCount = selectedCategories.length + selectedBrands.length +
        (hasPriceFilter ? 1 : 0) +
        (hasSearchFilter ? 1 : 0) +
        (currentCategorySlug ? 1 : 0) +
        (currentBrandSlug ? 1 : 0)

    const findCategoryBySlug = (slug: string): App.Data.Basic.CategoryData | undefined => {
        for (const cat of categories) {
            if (cat.slug === slug) return cat
            if (cat.children) {
                const found = cat.children.find(c => c.slug === slug)
                if (found) return found
            }
        }
        return undefined
    }

    const findBrandBySlug = (slug: string):  App.Data.Basic.BrandData | undefined => {
        return brands.find(b => b.slug === slug)
    }

    if (activeFiltersCount === 0) {
        return null
    }

    return (
        <div className="flex flex-wrap items-center gap-2 mb-6 animate-in fade-in slide-in-from-top-2">
            {currentCategorySlug && (
                <Badge variant="default" className="px-3 py-1 gap-2 text-sm font-normal bg-primary/20 text-primary border border-primary/30">
                    الفئة الحالية: {findCategoryBySlug(currentCategorySlug)?.name || currentCategorySlug}
                </Badge>
            )}
            {currentBrandSlug && (
                <Badge variant="default" className="px-3 py-1 gap-2 text-sm font-normal bg-primary/20 text-primary border border-primary/30">
                    العلامة الحالية: {findBrandBySlug(currentBrandSlug)?.name || currentBrandSlug}
                </Badge>
            )}
            {selectedCategories.length > 0 && (
                <Badge variant="secondary" className="px-3 py-1 gap-2 text-sm font-normal">
                    فئات إضافية: {selectedCategories.length}
                    <X
                        className="h-3.5 w-3.5 cursor-pointer hover:text-destructive transition-colors"
                        onClick={onCategoryClear}
                    />
                </Badge>
            )}
            {selectedBrands.length > 0 && (
                <Badge variant="secondary" className="px-3 py-1 gap-2 text-sm font-normal">
                    علامات إضافية: {selectedBrands.length}
                    <X
                        className="h-3.5 w-3.5 cursor-pointer hover:text-destructive transition-colors"
                        onClick={onBrandClear}
                    />
                </Badge>
            )}
            {hasPriceFilter && (
                <Badge variant="secondary" className="px-3 py-1 text-sm font-normal">
                    السعر: {priceRange![0]} - {priceRange![1]} ر.س
                </Badge>
            )}
            {hasSearchFilter && (
                <Badge variant="secondary" className="px-3 py-1 gap-2 text-sm font-normal">
                    بحث: {searchQuery}
                    {onSearchClear && (
                        <X
                            className="h-3.5 w-3.5 cursor-pointer hover:text-destructive transition-colors"
                            onClick={onSearchClear}
                        />
                    )}
                </Badge>
            )}
            <Button
                variant="link"
                size="sm"
                onClick={onClearAll}
                className="text-destructive hover:no-underline px-0 h-auto"
            >
                مسح جميع الفلاتر
            </Button>
        </div>
    )
}
