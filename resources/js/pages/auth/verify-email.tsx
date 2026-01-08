// Components
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/AuthLayout';
import { logout } from '@/routes';
import { send } from '@/routes/verification';
import { Form, Head } from '@inertiajs/react';

export default function VerifyEmail({ status }: { status?: string }) {
    return (
        <AuthLayout
            title="تأكيد البريد الإلكتروني"
            description="يرجى تأكيد بريدك الإلكتروني عبر الرابط المرسل إليك."
        >
            <Head title="تأكيد البريد الإلكتروني" />

            {status === 'verification-link-sent' && (
                <div className="mb-4 text-center text-sm font-medium text-green-600">
                    تم إرسال رابط تأكيد جديد إلى بريدك الإلكتروني المسجّل.
                </div>
            )}

            <Form
                action={send()}
                className="space-y-6 text-center"
            >
                {({ processing }) => (
                    <>
                        <Button
                            disabled={processing}
                            variant="secondary"
                        >
                            {processing && <Spinner />}
                            إعادة إرسال رابط التأكيد
                        </Button>

                        <TextLink
                            href={logout()}
                            className="mx-auto block text-sm"
                        >
                            تسجيل الخروج
                        </TextLink>
                    </>
                )}
            </Form>
        </AuthLayout>
    );
}
