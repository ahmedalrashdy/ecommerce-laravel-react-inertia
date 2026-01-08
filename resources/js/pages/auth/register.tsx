import { login } from '@/routes';
import { store } from '@/routes/register';
import { Form, Head } from '@inertiajs/react';

import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/AuthLayout';

export default function Register() {
    return (
        <AuthLayout
            title="إنشاء حساب جديد"
            description="أدخل بياناتك لإنشاء حسابك"
        >
            <Head title="إنشاء حساب" />
            <Form
                action={store()}
                resetOnSuccess={['password', 'password_confirmation']}
                disableWhileProcessing
                className="flex flex-col gap-6"
            >
                {({ processing, errors }) => (
                    <>
                        <div className="grid gap-6">
                            <div className="grid gap-2">
                                <Label htmlFor="name">الاسم الكامل</Label>
                                <Input
                                    id="name"
                                    type="text"
                                    required
                                    autoFocus
                                    tabIndex={1}
                                    autoComplete="name"
                                    name="name"
                                    placeholder="الاسم الكامل"
                                />
                                <InputError
                                    message={errors.name}
                                    className="mt-2"
                                />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="email">البريد الإلكتروني</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    required
                                    tabIndex={2}
                                    autoComplete="email"
                                    name="email"
                                    placeholder="name@example.com"
                                />
                                <InputError message={errors.email} />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="gender">الجنس</Label>
                                <select
                                    id="gender"
                                    name="gender"
                                    required
                                    tabIndex={3}
                                    defaultValue=""
                                    className="flex h-9 w-full min-w-0 rounded-md border border-input bg-transparent px-3 py-1 text-base text-foreground shadow-xs transition-[color,box-shadow] outline-none selection:bg-primary selection:text-primary-foreground placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 aria-invalid:border-destructive aria-invalid:ring-destructive/20 md:text-sm dark:border-input dark:bg-background dark:text-foreground dark:placeholder:text-muted-foreground dark:aria-invalid:ring-destructive/40"
                                >
                                    <option
                                        value=""
                                        disabled
                                    >
                                        اختر الجنس
                                    </option>
                                    <option value="male">ذكر</option>
                                    <option value="female">أنثى</option>
                                </select>
                                <InputError message={errors.gender} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="password">كلمة المرور</Label>
                                <Input
                                    id="password"
                                    type="password"
                                    required
                                    tabIndex={4}
                                    autoComplete="new-password"
                                    name="password"
                                    placeholder="••••••••"
                                />
                                <InputError message={errors.password} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="password_confirmation">
                                    تأكيد كلمة المرور
                                </Label>
                                <Input
                                    id="password_confirmation"
                                    type="password"
                                    required
                                    tabIndex={5}
                                    autoComplete="new-password"
                                    name="password_confirmation"
                                    placeholder="••••••••"
                                />
                                <InputError
                                    message={errors.password_confirmation}
                                />
                            </div>

                            <Button
                                type="submit"
                                className="mt-2 w-full"
                                tabIndex={6}
                                data-test="register-user-button"
                            >
                                {processing && <Spinner />}
                                إنشاء الحساب
                            </Button>
                        </div>

                        <div className="text-center text-sm text-muted-foreground">
                            لديك حساب بالفعل؟{' '}
                            <TextLink
                                href={login()}
                                tabIndex={7}
                            >
                                تسجيل الدخول
                            </TextLink>
                        </div>
                    </>
                )}
            </Form>
        </AuthLayout>
    );
}
