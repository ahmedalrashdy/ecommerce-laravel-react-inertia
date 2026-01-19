import AccountLayout from '@/features/account/layout/AccountLayout';
import AccountMobileMenu from '@/features/account/layout/AccountMobileMenu';
import { accountNavItems } from '@/features/account/layout/account-nav';
import { Button } from '@/components/ui/button';
import { Head, Link } from '@inertiajs/react';
import type { ReactNode } from 'react';

type AccountOverviewProps = {
    user: {
        name: string;
        email: string;
        avatar: string | null;
    };
};

function AccountOverview({ user }: AccountOverviewProps) {
    return (
        <>
            <Head title="حسابي" />
            <div className="hidden lg:block">
                <div className="rounded-3xl border border-border/60 bg-card p-8 shadow-sm">
                    <div className="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p className="text-xs text-muted-foreground">
                                نظرة عامة
                            </p>
                            <h1 className="text-3xl font-semibold">
                                مرحباً {user.name}
                            </h1>
                            <p className="mt-2 text-sm text-muted-foreground">
                                {user.email}
                            </p>
                        </div>
                        <div className="flex flex-wrap gap-3">
                            <Button asChild variant="outline">
                                <Link href="/account/notifications">
                                    إدارة الإشعارات
                                </Link>
                            </Button>
                            <Button asChild>
                                <Link href="/account/orders">عرض الطلبات</Link>
                            </Button>
                        </div>
                    </div>
                </div>

                <div className="mt-6 grid gap-4 md:grid-cols-2">
                    {accountNavItems.map((item) => {
                        const Icon = item.icon;

                        return (
                            <Link
                                key={item.key}
                                href={item.href}
                                className="group rounded-3xl border border-border/60 bg-card p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
                            >
                                <div className="flex items-center justify-between">
                                    <div className="space-y-2">
                                        <p className="text-xs text-muted-foreground">
                                            الحساب
                                        </p>
                                        <h2 className="text-lg font-semibold">
                                            {item.label}
                                        </h2>
                                    </div>
                                    <div className="flex size-10 items-center justify-center rounded-full bg-primary/10 text-primary transition group-hover:bg-primary/15">
                                        <Icon className="size-5" />
                                    </div>
                                </div>
                            </Link>
                        );
                    })}
                </div>
            </div>

            <div className="block lg:hidden">
                <AccountMobileMenu />
            </div>
        </>
    );
}

AccountOverview.layout = (page: ReactNode) => (
    <AccountLayout>{page}</AccountLayout>
);

export default AccountOverview;
