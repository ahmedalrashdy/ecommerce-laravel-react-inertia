export interface UserAddress {
    id: number;
    user_id: number;
    contact_person: string;
    contact_phone: string;
    country: string | null;
    state: string | null;
    city: string | null;
    postal_code: string | null;
    address_line_1: string;
    address_line_2: string | null;
    is_default_shipping: boolean;
    created_at: string;
    updated_at: string;
}

export interface AddressFormData {
    contact_person: string;
    contact_phone: string;
    country: string | null;
    state: string | null;
    city: string | null;
    postal_code: string | null;
    address_line_1: string;
    address_line_2: string | null;
    is_default_shipping: boolean;
}
