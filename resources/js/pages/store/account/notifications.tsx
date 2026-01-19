import AccountLayout from '@/features/account/layout/AccountLayout';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Spinner } from '@/components/ui/spinner';
import { Head, useForm } from '@inertiajs/react';
import type { FormEvent, ReactNode } from 'react';

type NotificationPreferences = {
    marketing_email: boolean;
    marketing_sms: boolean;
    marketing_whatsapp: boolean;
    marketing_call: boolean;
};

type NotificationsPageProps = {
    preferences: NotificationPreferences;
};

function NotificationsPage({ preferences }: NotificationsPageProps) {
    const { data, setData, patch, processing, errors } =
        useForm<NotificationPreferences>({
            marketing_email: preferences.marketing_email,
            marketing_sms: preferences.marketing_sms,
            marketing_whatsapp: preferences.marketing_whatsapp,
            marketing_call: preferences.marketing_call,
        });

    const handleSubmit = (event: FormEvent) => {
        event.preventDefault();
        patch('/account/notifications');
    };

    return (
        <>
            <Head title="خيارات الإشعارات" />
            <div className="rounded-3xl border border-border/60 bg-card p-6 shadow-sm lg:p-8">
                <div className="mb-6 space-y-2">
                    <p className="text-xs text-muted-foreground">
                        إعدادات التواصل
                    </p>
                    <h1 className="text-2xl font-semibold">
                        الاشتراك في النشرة الإخبارية
                    </h1>
                    <p className="text-sm text-muted-foreground">
                        اختر طريقة التواصل التي ترغب باستقبالها.
                    </p>
                </div>

                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="rounded-2xl border border-border/60">
                        <div className="border-b border-border/60 px-4 py-4">
                            <h2 className="text-sm font-semibold">
                                البريد الإلكتروني
                            </h2>
                            <p className="text-xs text-muted-foreground">
                                العروض، التحديثات، ورسائل الحملات الترويجية.
                            </p>
                        </div>
                        <div className="flex items-center justify-between px-4 py-4">
                            <div>
                                <p className="text-sm font-medium">
                                    البريد الإلكتروني
                                </p>
                                <p className="text-xs text-muted-foreground">
                                    أريد استلام رسائل بريد إلكترونية عن العروض.
                                </p>
                            </div>
                            <Checkbox
                                checked={data.marketing_email}
                                onCheckedChange={(checked) =>
                                    setData(
                                        'marketing_email',
                                        checked === true,
                                    )
                                }
                            />
                        </div>
                    </div>

                    <div className="rounded-2xl border border-border/60">
                        <div className="border-b border-border/60 px-4 py-4">
                            <h2 className="text-sm font-semibold">
                                الرسائل القصيرة
                            </h2>
                            <p className="text-xs text-muted-foreground">
                                تنبيهات سريعة حول العروض والأخبار المهمة.
                            </p>
                        </div>
                        <div className="flex items-center justify-between px-4 py-4">
                            <div>
                                <p className="text-sm font-medium">
                                    الرسائل القصيرة
                                </p>
                                <p className="text-xs text-muted-foreground">
                                    أريد استلام رسائل قصيرة حول العروض.
                                </p>
                            </div>
                            <Checkbox
                                checked={data.marketing_sms}
                                onCheckedChange={(checked) =>
                                    setData(
                                        'marketing_sms',
                                        checked === true,
                                    )
                                }
                            />
                        </div>
                    </div>

                    <div className="rounded-2xl border border-border/60">
                        <div className="border-b border-border/60 px-4 py-4">
                            <h2 className="text-sm font-semibold">WhatsApp</h2>
                            <p className="text-xs text-muted-foreground">
                                رسائل واتساب حول العروض والخدمات الخاصة.
                            </p>
                        </div>
                        <div className="flex items-center justify-between px-4 py-4">
                            <div>
                                <p className="text-sm font-medium">WhatsApp</p>
                                <p className="text-xs text-muted-foreground">
                                    أريد استلام إشعارات واتساب ترويجية.
                                </p>
                            </div>
                            <Checkbox
                                checked={data.marketing_whatsapp}
                                onCheckedChange={(checked) =>
                                    setData(
                                        'marketing_whatsapp',
                                        checked === true,
                                    )
                                }
                            />
                        </div>
                    </div>

                    <div className="rounded-2xl border border-border/60">
                        <div className="border-b border-border/60 px-4 py-4">
                            <h2 className="text-sm font-semibold">
                                مكالمة هاتفية
                            </h2>
                            <p className="text-xs text-muted-foreground">
                                تواصل هاتفي بخصوص العروض والخدمات.
                            </p>
                        </div>
                        <div className="flex items-center justify-between px-4 py-4">
                            <div>
                                <p className="text-sm font-medium">
                                    مكالمة هاتفية
                                </p>
                                <p className="text-xs text-muted-foreground">
                                    أريد تلقي مكالمات هاتفية حول العروض.
                                </p>
                            </div>
                            <Checkbox
                                checked={data.marketing_call}
                                onCheckedChange={(checked) =>
                                    setData(
                                        'marketing_call',
                                        checked === true,
                                    )
                                }
                            />
                        </div>
                    </div>

                    <InputError
                        message={
                            errors.marketing_email ||
                            errors.marketing_sms ||
                            errors.marketing_whatsapp ||
                            errors.marketing_call
                        }
                    />

                    <div className="flex justify-start">
                        <Button type="submit" disabled={processing}>
                            {processing && <Spinner />}
                            تحديث
                        </Button>
                    </div>
                </form>
            </div>
        </>
    );
}

NotificationsPage.layout = (page: ReactNode) => (
    <AccountLayout>{page}</AccountLayout>
);

export default NotificationsPage;
