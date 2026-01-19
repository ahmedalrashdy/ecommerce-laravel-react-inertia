import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { formatAddress } from '@/lib/address-utils';
import type { UserAddress } from '@/types/address';
import { router } from '@inertiajs/react';
import { MapPin, Trash2, Truck } from 'lucide-react';

interface AddressCardProps {
    address: UserAddress;
}

export function AddressCard({ address }: AddressCardProps) {
    const handleDelete = () => {
        if (confirm('هل أنت متأكد من حذف هذا العنوان؟')) {
            router.delete(`/account/addresses/${address.id}`);
        }
    };

    const handleSetDefaultShipping = () => {
        router.patch(
            `/account/addresses/${address.id}/set-default-shipping`,
            {},
            {
                preserveScroll: true,
            },
        );
    };

    return (
        <Card className="relative">
            <CardHeader>
                <div className="flex items-start justify-between">
                    <div className="flex-1">
                        <CardTitle className="flex items-center gap-2">
                            <MapPin className="size-4" />
                            عنوان الشحن
                        </CardTitle>
                        <CardDescription className="mt-1">
                            {formatAddress(address)}
                        </CardDescription>
                    </div>
                    {address.is_default_shipping && (
                        <span className="inline-flex items-center gap-1 rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            <Truck className="size-3" />
                            افتراضي للشحن
                        </span>
                    )}
                </div>
            </CardHeader>
            <CardContent>
                <div className="space-y-2 text-sm">
                    <div>
                        <span className="font-medium">جهة الاتصال:</span>{' '}
                        {address.contact_person}
                    </div>
                    <div>
                        <span className="font-medium">رقم الهاتف:</span>{' '}
                        {address.contact_phone}
                    </div>
                    {address.postal_code && (
                        <div>
                            <span className="font-medium">الرمز البريدي:</span>{' '}
                            {address.postal_code}
                        </div>
                    )}
                </div>
            </CardContent>
            <CardFooter className="flex gap-2">
                {!address.is_default_shipping && (
                    <Button
                        variant="outline"
                        size="sm"
                        onClick={handleSetDefaultShipping}
                        className="flex-1"
                    >
                        <Truck className="size-4" />
                        افتراضي شحن
                    </Button>
                )}
                <Button
                    variant="destructive"
                    size="sm"
                    onClick={handleDelete}
                >
                    <Trash2 className="size-4" />
                </Button>
            </CardFooter>
        </Card>
    );
}
