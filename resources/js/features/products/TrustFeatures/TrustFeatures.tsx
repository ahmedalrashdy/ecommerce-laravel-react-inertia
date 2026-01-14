import { Package, RotateCcw, Shield, Truck } from 'lucide-react';

export function TrustFeatures() {
    return (
        <section className="relative z-20 mx-4 -mt-8 max-w-7xl rounded-xl border-b border-border bg-card shadow-sm md:mx-auto">
            <div className="grid grid-cols-2 divide-x divide-border/50 divide-x-reverse md:grid-cols-4">
                <div className="flex flex-col items-center gap-3 rounded-r-xl p-6 text-center transition-colors hover:bg-muted/30">
                    <div className="flex h-12 w-12 items-center justify-center rounded-full bg-primary/10 text-primary">
                        <Truck className="h-6 w-6" />
                    </div>
                    <div>
                        <h3 className="font-bold text-foreground">
                            شحن مجاني وسريع
                        </h3>
                        <p className="mt-1 text-xs text-muted-foreground">
                            للطلبات فوق $200
                        </p>
                    </div>
                </div>
                <div className="flex flex-col items-center gap-3 p-6 text-center transition-colors hover:bg-muted/30">
                    <div className="flex h-12 w-12 items-center justify-center rounded-full bg-blue-500/10 text-blue-500">
                        <Shield className="h-6 w-6" />
                    </div>
                    <div>
                        <h3 className="font-bold text-foreground">ضمان ذهبي</h3>
                        <p className="mt-1 text-xs text-muted-foreground">
                            منتجات أصلية 100%
                        </p>
                    </div>
                </div>
                <div className="flex flex-col items-center gap-3 p-6 text-center transition-colors hover:bg-muted/30">
                    <div className="flex h-12 w-12 items-center justify-center rounded-full bg-amber-500/10 text-amber-500">
                        <RotateCcw className="h-6 w-6" />
                    </div>
                    <div>
                        <h3 className="font-bold text-foreground">
                            استرجاع سهل
                        </h3>
                        <p className="mt-1 text-xs text-muted-foreground">
                            خلال 14 يوم من الشراء
                        </p>
                    </div>
                </div>
                <div className="flex flex-col items-center gap-3 rounded-l-xl p-6 text-center transition-colors hover:bg-muted/30">
                    <div className="flex h-12 w-12 items-center justify-center rounded-full bg-green-500/10 text-green-500">
                        <Package className="h-6 w-6" />
                    </div>
                    <div>
                        <h3 className="font-bold text-foreground">تغليف آمن</h3>
                        <p className="mt-1 text-xs text-muted-foreground">
                            يصلك المنتج بحالة ممتازة
                        </p>
                    </div>
                </div>
            </div>
        </section>
    );
}
