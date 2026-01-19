import { AddressForm } from '@/features/account/addresses/AddressForm';
import { Breadcrumbs } from '@/components/breadcrumbs';
import { Head, router } from '@inertiajs/react';
import AccountLayout from '@/features/account/layout/AccountLayout';
import type { ReactNode } from 'react';

function CreateAddress() {
    const handleCancel = () => {
        router.visit('/account/addresses');
    };

    return (
        <>
            <Head title="إضافة عنوان جديد" />
            <Breadcrumbs
                breadcrumbs={[
                    { title: 'الرئيسية', href: '/' },
                    { title: 'عناويني', href: '/account/addresses' },
                    {
                        title: 'إضافة عنوان جديد',
                        href: '/account/addresses/create',
                    },
                ]}
            />
            <div className="mx-auto mt-6 max-w-3xl rounded-3xl border border-border/60 bg-card p-6 shadow-sm">
                <h1 className="mb-6 text-2xl font-semibold">
                    إضافة عنوان جديد
                </h1>
                <AddressForm
                    action="/account/addresses"
                    method="post"
                    onCancel={handleCancel}
                />
            </div>
        </>
    );
}

CreateAddress.layout = (page: ReactNode) => (
    <AccountLayout>{page}</AccountLayout>
);

export default CreateAddress;
