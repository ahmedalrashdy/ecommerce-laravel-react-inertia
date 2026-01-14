import * as React from "react"
import { List, SlidersHorizontal, ArrowUpDown, LayoutGrid } from "lucide-react"
import { Button } from "@/components/ui/button"
import { Badge } from "@/components/ui/badge"
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetTrigger } from "@/components/ui/sheet"
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select"
import { cn } from "@/lib/utils"
import { FilterSidebar } from "../FilterSidebar/FilterSidebar"

interface ShopToolbarProps {
    viewMode: "grid" | "list"
    onViewModeChange: (mode: "grid" | "list") => void
    sortBy: string
    onSortChange: (sort: string) => void
    isFilterOpen: boolean
    onFilterOpenChange: (open: boolean) => void
    activeFiltersCount: number
    mainCategories:App.Data.Basic.CategoryData[]
    featuredBrands?: App.Data.Basic.BrandData[]
    priceRange: [number, number]
    maxPrice: number
    onPriceChange: (range: [number, number]) => void
    selectedCategories: string[]
    onCategoryChange: (slugs: string[]) => void
    selectedBrands: string[]
    onBrandChange: (slugs: string[]) => void
    onClearAll: () => void
    productsCount: number
    currentCategorySlug?: string | null
    currentBrandSlug?: string | null
}

export function ShopToolbar({
    viewMode,
    onViewModeChange,
    sortBy,
    onSortChange,
    isFilterOpen,
    onFilterOpenChange,
    activeFiltersCount,
    mainCategories,
    featuredBrands = [],
    priceRange,
    maxPrice,
    onPriceChange,
    selectedCategories,
    onCategoryChange,
    selectedBrands,
    onBrandChange,
    onClearAll,
    productsCount,
    currentCategorySlug,
    currentBrandSlug,
}: ShopToolbarProps) {
    return (
        <div className="flex flex-col sm:flex-row items-center justify-between gap-4 mb-6 p-4 bg-card border border-border rounded-xl shadow-sm  ">
            <div className="flex items-center gap-2 w-full sm:w-auto">
                {/* Mobile Filter Trigger */}
                <Sheet open={isFilterOpen} onOpenChange={onFilterOpenChange}>
                    <SheetTrigger asChild>
                        <Button variant="outline" className="lg:hidden w-full sm:w-auto gap-2">
                            <SlidersHorizontal className="h-4 w-4" />
                            الفلترة
                            {activeFiltersCount > 0 && (
                                <Badge className="h-5 w-5 p-0 flex items-center justify-center text-[10px] bg-primary rounded-full">
                                    {activeFiltersCount}
                                </Badge>
                            )}
                        </Button>
                    </SheetTrigger>
                    <SheetContent side="right" className="w-full sm:w-[400px] overflow-y-auto">
                        <SheetHeader className="mb-6 border-b border-border pb-4">
                            <SheetTitle>فلترة المنتجات</SheetTitle>
                        </SheetHeader>
                        <FilterSidebar
                            categories={mainCategories}
                            brands={featuredBrands}
                            priceRange={priceRange}
                            maxPrice={maxPrice}
                            onPriceChange={onPriceChange}
                            selectedCategories={selectedCategories}
                            onCategoryChange={onCategoryChange}
                            selectedBrands={selectedBrands}
                            onBrandChange={onBrandChange}
                            onClearAll={onClearAll}
                            currentCategorySlug={currentCategorySlug}
                            currentBrandSlug={currentBrandSlug}
                        />
                    </SheetContent>
                </Sheet>

                <span className="text-sm text-muted-foreground hidden sm:inline-block">
                    عرض <span className="font-bold text-foreground">{productsCount}</span> منتج
                </span>
            </div>

            <div className="flex items-center gap-3 w-full sm:w-auto justify-end">
                <Select value={sortBy} onValueChange={onSortChange}>
                    <SelectTrigger className="w-[160px]">
                        <ArrowUpDown className="h-3.5 w-3.5 ml-2 text-muted-foreground" />
                        <SelectValue placeholder="ترتيب حسب" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="new-arrivals">الأحدث</SelectItem>
                        <SelectItem value="best-sellers">الأكثر مبيعاً</SelectItem>
                        <SelectItem value="rating">الأعلى تقيماً</SelectItem>
                        <SelectItem value="price-low">الأقل سعراً</SelectItem>
                        <SelectItem value="price-high">الأعلى سعراً</SelectItem>
                    </SelectContent>
                </Select>

                <div className="flex items-center bg-muted p-1 rounded-lg border border-border">
                    <button
                        onClick={() => onViewModeChange("grid")}
                        className={cn(
                            "p-2 rounded-md transition-all",
                            viewMode === "grid" ? "bg-background text-foreground shadow-sm" : "text-muted-foreground hover:text-foreground"
                        )}
                        title="شبكة"
                    >
                        <LayoutGrid className="h-4 w-4" />
                    </button>
                    <button
                        onClick={() => onViewModeChange("list")}
                        className={cn(
                            "p-2 rounded-md transition-all",
                            viewMode === "list" ? "bg-background text-foreground shadow-sm" : "text-muted-foreground hover:text-foreground"
                        )}
                        title="قائمة"
                    >
                        <List className="h-4 w-4" />
                    </button>
                </div>
            </div>
        </div>
    )
}
