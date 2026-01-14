import * as React from "react"
import { Badge } from "@/components/ui/badge"

interface HeroBannerProps {
    title?: string | null
    description?: string | null
}

export function HeroBanner({ title, description }: HeroBannerProps) {
    const displayTitle = title || "منتجات مميزة"
    const displayDescription = description || "تصفح مجموعتنا المختارة بعناية من أفضل المنتجات التقنية والأزياء العصرية بجودة لا تضاهى."

    return (
        <section className="relative bg-black dark:bg-background overflow-hidden py-16 md:py-20 text-white">
            <div className="absolute inset-0 z-0">
                {/* Background Pattern */}
                <div className="absolute inset-0 opacity-20" style={{ backgroundImage: "radial-gradient(#ffffff 1px, transparent 1px)", backgroundSize: "32px 32px" }}></div>
                {/* Glow Effects */}
                <div className="absolute -top-24 -right-24 w-96 h-96 bg-primary/40 rounded-full blur-[100px]" />
                <div className="absolute -bottom-24 -left-24 w-96 h-96 bg-accent/40 rounded-full blur-[100px]" />
            </div>

            <div className="container mx-auto px-4 relative z-10">
                <div className="flex flex-col items-center text-center max-w-3xl mx-auto space-y-4">
                    <Badge variant="outline" className="text-white border-white/30 backdrop-blur-sm px-4 py-1 mb-2">
                        🎉 عروض الموسم الجديد
                    </Badge>
                    <h1 className="text-4xl md:text-5xl lg:text-6xl font-black tracking-tight mb-2 leading-tight">
                        اكتشف <span className="text-transparent bg-clip-text bg-gradient-to-r from-primary to-accent">{displayTitle}</span>
                    </h1>
                    <p className="text-white/70 text-lg md:text-xl max-w-2xl leading-relaxed">
                        {displayDescription}
                    </p>
                </div>
            </div>
        </section>
    )
}

