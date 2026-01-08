// Components
import { login } from '@/routes';
import { email as emailRoute } from '@/routes/password';
import { Form, Head } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';

import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout';

export default function ForgotPassword({
    status,
    email,
}: {
    status?: string;
    email?: string;
}) {
    return (
        <AuthLayout
            title="نسيت كلمة المرور"
            description="أدخل بريدك الإلكتروني لاستلام رابط إعادة التعيين"
        >
            <Head title="نسيت كلمة المرور" />

            {status && (
                <div className="mb-4 text-center text-sm font-medium text-green-600">
                    {status}
                </div>
            )}

            <div className="space-y-6">
                <Form action={emailRoute()}>
                    {({ processing, errors }) => (
                        <>
                            <div className="grid gap-2">
                                <Label htmlFor="email">البريد الإلكتروني</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    name="email"
                                    autoComplete="off"
                                    autoFocus
                                    placeholder="name@example.com"
                                    defaultValue={email ?? ''}
                                />

                                <InputError message={errors.email} />
                            </div>

                            <div className="my-6 flex items-center justify-start">
                                <Button
                                    className="w-full"
                                    disabled={processing}
                                    data-test="email-password-reset-link-button"
                                >
                                    {processing && (
                                        <LoaderCircle className="h-4 w-4 animate-spin" />
                                    )}
                                    إرسال رابط إعادة التعيين
                                </Button>
                            </div>
                        </>
                    )}
                </Form>

                <div className="space-x-1 text-center text-sm text-muted-foreground">
                    <span>أو الرجوع إلى</span>
                    <TextLink href={login()}>تسجيل الدخول</TextLink>
                </div>
            </div>
        </AuthLayout>
    );
}
