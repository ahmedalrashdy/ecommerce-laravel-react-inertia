import * as React from "react"
import {
    Star,
    Check,
    ThumbsUp,
    ThumbsDown,
    ZoomIn,
} from "lucide-react"
import { Badge } from "@/components/ui/badge"
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"
import { storageUrl } from "@/lib/utils"





export const RatingStars: React.FC<{ rating: number; size?: "sm" | "md" | "lg" }> = ({ rating, size = "md" }) => {
    const sizes = {
        sm: "h-3.5 w-3.5",
        md: "h-4 w-4",
        lg: "h-5 w-5",
    }

    return (
        <div className="flex items-center gap-0.5">
            {[...Array(5)].map((_, i) => (
                <Star
                    key={i}
                    className={`${sizes[size]} ${i < Math.floor(rating)
                        ? "text-warning fill-warning"
                        : i < rating
                            ? "text-warning fill-warning/50"
                            : "text-muted"
                        }`}
                />
            ))}
        </div>
    )
}

// ============================================
// ReviewCard Component - Enhanced
// ============================================
export const ReviewCard: React.FC<{ review: Review }> = ({ review }) => {
    const [isHelpful, setIsHelpful] = React.useState<boolean | null>(null)

    return (
        <div className="p-4 sm:p-6 bg-gradient-to-br from-card to-card/80 border border-border/50 rounded-2xl hover:shadow-lg hover:shadow-primary/5 transition-all duration-300 group">
            <div className="flex items-start gap-3 sm:gap-4">
                <Avatar className="h-10 w-10 sm:h-12 sm:w-12 ring-2 ring-primary/10 ring-offset-2 ring-offset-background">
                    <AvatarImage src={storageUrl(review.user.avatar)} />
                    <AvatarFallback className="bg-gradient-to-br from-primary/20 to-primary/10 text-primary font-bold">
                        {review.user.name.charAt(0)}
                    </AvatarFallback>
                </Avatar>
                <div className="flex-1 min-w-0">
                    <div className="flex flex-wrap items-center gap-2 mb-1">
                        <span className="font-semibold text-foreground">{review.user.name}</span>
                        {review.verified && (
                            <Badge className="gap-1 text-[10px] h-5 bg-success/10 text-success border-success/20 hover:bg-success/20">
                                <Check className="h-3 w-3" />
                                مشتري موثق
                            </Badge>
                        )}
                        <span className="text-xs text-muted-foreground mr-auto">{review.date}</span>
                    </div>
                    <RatingStars rating={review.rating} size="sm" />
                </div>
            </div>

            <p className="mt-4 text-sm sm:text-base text-foreground/90 leading-relaxed">{review.comment}</p>

            {review.images && review.images.length > 0 && (
                <div className="flex gap-2 mt-4 overflow-x-auto scrollbar-hide">
                    {review.images.map((img, i) => (
                        <div key={i} className="relative flex-shrink-0 w-16 h-16 sm:w-20 sm:h-20 rounded-lg overflow-hidden border border-border group/img cursor-pointer">
                            <img src={storageUrl(img)} 
                            alt=""
                            loading="lazy"
                            decoding="async"
                             className="w-full h-full object-cover group-hover/img:scale-110 transition-transform duration-300" />
                            <div className="absolute inset-0 flex items-center justify-center bg-black/0 group-hover/img:bg-black/40 transition-colors">
                                <ZoomIn className="h-5 w-5 text-white opacity-0 group-hover/img:opacity-100 transition-opacity" />
                            </div>
                        </div>
                    ))}
                </div>
            )}

            <div className="flex items-center gap-2 sm:gap-4 mt-4 pt-4 border-t border-border/50">
                <span className="text-xs sm:text-sm text-muted-foreground">هل كان هذا التقييم مفيداً؟</span>
                <div className="flex items-center gap-2 mr-auto">
                    <button
                        onClick={() => setIsHelpful(true)}
                        className={`flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs sm:text-sm transition-all ${isHelpful === true
                            ? 'bg-success/10 text-success'
                            : 'text-muted-foreground hover:bg-success/10 hover:text-success'
                            }`}
                    >
                        <ThumbsUp className="h-3.5 w-3.5" />
                        <span>{review.helpful + (isHelpful === true ? 1 : 0)}</span>
                    </button>
                    <button
                        onClick={() => setIsHelpful(false)}
                        className={`flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs sm:text-sm transition-all ${isHelpful === false
                            ? 'bg-destructive/10 text-destructive'
                            : 'text-muted-foreground hover:bg-destructive/10 hover:text-destructive'
                            }`}
                    >
                        <ThumbsDown className="h-3.5 w-3.5" />
                        <span>{review.notHelpful + (isHelpful === false ? 1 : 0)}</span>
                    </button>
                </div>
            </div>
        </div>
    )
}
