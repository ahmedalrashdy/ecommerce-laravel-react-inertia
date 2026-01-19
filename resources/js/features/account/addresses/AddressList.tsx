import { Button } from '@/components/ui/button';
import type { UserAddress } from '@/types/address';
import { router } from '@inertiajs/react';
import { MapPin, Plus } from 'lucide-react';
import { AddressCard } from './AddressCard';

interface AddressListProps {
    addresses: UserAddress[];
}

export function AddressList({ addresses }: AddressListProps) {
    const handleAddNew = () => {
        router.visit('/account/addresses/create');
    };

    if (addresses.length === 0) {
        return (
            <div className="flex flex-col items-center justify-center rounded-lg border border-dashed p-12 text-center">
                <MapPin className="mb-4 size-12 text-muted-foreground" />
                <h3 className="mb-2 text-lg font-semibold">لا توجد عناوين</h3>
                <p className="mb-4 text-sm text-muted-foreground">
                    لم تقم بإضافة أي عناوين بعد. أضف عنوانك الأول للبدء.
                </p>
                <Button onClick={handleAddNew}>
                    <Plus className="size-4" />
                    إضافة عنوان جديد
                </Button>
            </div>
        );
    }

    return (
        <div className="space-y-4">
            <div className="flex items-center justify-between">
                <h2 className="text-lg font-semibold">
                    عناويني ({addresses.length})
                </h2>
                <Button onClick={handleAddNew}>
                    <Plus className="size-4" />
                    إضافة عنوان جديد
                </Button>
            </div>
            <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                {addresses.map((address) => (
                    <AddressCard
                        key={address.id}
                        address={address}
                    />
                ))}
            </div>
        </div>
    );
}
