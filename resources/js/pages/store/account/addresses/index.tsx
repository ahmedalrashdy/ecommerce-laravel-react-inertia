import { AddressList } from '@/features/account/addresses/AddressList';
import { Breadcrumbs } from '@/components/breadcrumbs';
import type { UserAddress } from '@/types/address';
import { Head } from '@inertiajs/react';
import AccountLayout from '@/features/account/layout/AccountLayout';
import type { ReactNode } from 'react';

interface AddressesIndexProps {
    addresses: UserAddress[];
}

function AddressesIndex({ addresses }: AddressesIndexProps) {
    return (
        <>
            <Head title="عناويني" />
            <Breadcrumbs
                breadcrumbs={[
                    { title: 'الرئيسية', href: '/' },
                    { title: 'عناويني', href: '/account/addresses' },
                ]}
            />
            <div className="mt-6">
                <AddressList addresses={addresses} />
            </div>
        </>
    );
}

AddressesIndex.layout = (page: ReactNode) => (
    <AccountLayout>{page}</AccountLayout>
);

export default AddressesIndex;
