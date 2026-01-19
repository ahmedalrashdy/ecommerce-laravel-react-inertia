import type { AddressFormData, UserAddress } from '@/types/address';
import { useCallback, useState } from 'react';

interface UseAddressFormOptions {
    initialData?: Partial<AddressFormData> | UserAddress;
    onSubmit?: (data: AddressFormData) => void;
}

export function useAddressForm(options: UseAddressFormOptions = {}) {
    const { initialData } = options;

    const getInitialValue = (key: keyof AddressFormData): any => {
        if (!initialData) {
            return key.includes('is_default') ? false : '';
        }

        // Handle UserAddress type
        if ('id' in initialData) {
            const address = initialData as UserAddress;
            return address[key as keyof UserAddress] ?? (key.includes('is_default') ? false : '');
        }

        // Handle AddressFormData type
        return (initialData as Partial<AddressFormData>)[key] ?? (key.includes('is_default') ? false : '');
    };

    const [formData, setFormData] = useState<AddressFormData>({
        contact_person: getInitialValue('contact_person') as string,
        contact_phone: getInitialValue('contact_phone') as string,
        country: getInitialValue('country') as string | null,
        state: getInitialValue('state') as string | null,
        city: getInitialValue('city') as string | null,
        postal_code: getInitialValue('postal_code') as string | null,
        address_line_1: getInitialValue('address_line_1') as string,
        address_line_2: getInitialValue('address_line_2') as string | null,
        is_default_shipping: getInitialValue('is_default_shipping') as boolean,
    });

    const updateField = useCallback(<K extends keyof AddressFormData>(
        field: K,
        value: AddressFormData[K],
    ) => {
        setFormData((prev) => ({
            ...prev,
            [field]: value,
        }));
    }, []);

    const reset = useCallback(() => {
        setFormData({
            contact_person: '',
            contact_phone: '',
            country: null,
            state: null,
            city: null,
            postal_code: null,
            address_line_1: '',
            address_line_2: null,
            is_default_shipping: false,
        });
    }, []);

    return {
        formData,
        updateField,
        reset,
        setFormData,
    };
}
