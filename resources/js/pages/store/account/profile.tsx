import AccountLayout from '@/features/account/layout/AccountLayout';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { Form, Head } from '@inertiajs/react';
import type { ReactNode } from 'react';

type ProfilePageProps = {
    user: {
        name: string;
        email: string;
    };
    mustVerifyEmail: boolean;
    status: string | null;
};

function ProfilePage({ user, mustVerifyEmail, status }: ProfilePageProps) {
    return (
        <>
            <Head title="بياناتي الشخصية" />
            <div className="rounded-3xl border border-border/60 bg-card p-6 shadow-sm lg:p-8">
                <div className="mb-6 space-y-2">
                    <p className="text-xs text-muted-foreground">
                        إعدادات الحساب
                    </p>
                    <h1 className="text-2xl font-semibold">بياناتي الشخصية</h1>
                    <p className="text-sm text-muted-foreground">
                        حدّث بياناتك لتبقى معلومات حسابك محدثة دائماً.
                    </p>
                </div>

                {mustVerifyEmail && status === 'verification-link-sent' && (
                    <div className="mb-6 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                        تم إرسال رابط التحقق إلى بريدك الإلكتروني.
                    </div>
                )}

                <Form
                    method="patch"
                    action="/account/profile"
                    className="space-y-6"
                >
                    {({ processing, errors }) => (
                        <>
                            <div className="grid gap-6 md:grid-cols-2">
                                <div className="grid gap-2">
                                    <Label htmlFor="name">الاسم الكامل</Label>
                                    <Input
                                        id="name"
                                        name="name"
                                        defaultValue={user.name}
                                        required
                                    />
                                    <InputError message={errors.name} />
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="email">
                                        البريد الإلكتروني
                                    </Label>
                                    <Input
                                        id="email"
                                        name="email"
                                        type="email"
                                        defaultValue={user.email}
                                        required
                                    />
                                    <InputError message={errors.email} />
                                </div>
                            </div>

                            <div className="flex justify-start">
                                <Button type="submit" disabled={processing}>
                                    {processing && <Spinner />}
                                    حفظ التحديثات
                                </Button>
                            </div>
                        </>
                    )}
                </Form>
            </div>
        </>
    );
}

ProfilePage.layout = (page: ReactNode) => (
    <AccountLayout>{page}</AccountLayout>
);

export default ProfilePage;
