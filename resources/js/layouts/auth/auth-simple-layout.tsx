import store from '@/routes/store';
import { Link } from '@inertiajs/react';
import { BadgeCheck, CreditCard, Package, Truck } from 'lucide-react';
import { type PropsWithChildren } from 'react';

interface AuthLayoutProps {
    name?: string;
    title?: string;
    description?: string;
    variant?: 'full' | 'embedded';
}

export default function AuthSimpleLayout({
    children,
    title,
    description,
    variant = 'full',
}: PropsWithChildren<AuthLayoutProps>) {
    const isEmbedded = variant === 'embedded';

    return (
        <div
            className={
                isEmbedded
                    ? 'w-full'
                    : 'flex min-h-svh flex-col items-center justify-center gap-6 bg-background p-6 md:p-10'
            }
        >
            <div className={isEmbedded ? 'mx-auto w-full max-w-sm' : 'w-full max-w-sm'}>
                <div className="flex flex-col gap-8">
                    <div className="flex flex-col items-center gap-4">
                        <Link
                            href={store.home().url}
                            className="flex flex-col items-center gap-2 font-medium"
                        >
                            <div className="mb-1 flex h-9 w-9 items-center justify-center rounded-md">
                                {/* <AppLogoIcon className="size-9 fill-current text-[var(--foreground)] dark:text-white" /> */}
                            </div>
                            <span className="sr-only">{title}</span>
                        </Link>

                        <div className="space-y-2 text-center">
                            <h1 className="text-xl font-medium">{title}</h1>
                            <p className="text-center text-sm text-muted-foreground">
                                {description}
                            </p>
                            <div className="mt-3 flex flex-wrap justify-center gap-2">
                                <span className="inline-flex items-center gap-1.5 rounded-full border border-border/60 bg-muted/40 px-2.5 py-1 text-[11px] font-semibold text-foreground">
                                    <Truck className="h-3.5 w-3.5 text-primary" />
                                    شحن سريع
                                </span>
                                <span className="inline-flex items-center gap-1.5 rounded-full border border-border/60 bg-muted/40 px-2.5 py-1 text-[11px] font-semibold text-foreground">
                                    <Package className="h-3.5 w-3.5 text-primary" />
                                    تتبع الطلبات
                                </span>
                                <span className="inline-flex items-center gap-1.5 rounded-full border border-border/60 bg-muted/40 px-2.5 py-1 text-[11px] font-semibold text-foreground">
                                    <CreditCard className="h-3.5 w-3.5 text-primary" />
                                    دفع آمن
                                </span>
                                <span className="inline-flex items-center gap-1.5 rounded-full border border-border/60 bg-muted/40 px-2.5 py-1 text-[11px] font-semibold text-foreground">
                                    <BadgeCheck className="h-3.5 w-3.5 text-primary" />
                                    ضمان الجودة
                                </span>
                            </div>
                        </div>
                    </div>
                    {children}
                </div>
            </div>
        </div>
    );
}
