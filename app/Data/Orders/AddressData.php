<?php

namespace App\Data\Orders;

use App\Models\UserAddress;
use Spatie\LaravelData\Data;

class AddressData extends Data
{
    public function __construct(
        public string $contact_person,
        public string $contact_phone,
        public string $address_line_1,
        public ?string $address_line_2,
        public ?string $city,
        public ?string $state,
        public ?string $postal_code,
        public ?string $country,
    ) {}

    public static function fromModel(UserAddress $address): self
    {
        return new self(
            contact_person: $address->contact_person,
            contact_phone: $address->contact_phone,
            address_line_1: $address->address_line_1,
            address_line_2: $address->address_line_2,
            city: $address->city,
            state: $address->state,
            postal_code: $address->postal_code,
            country: $address->country,
        );
    }
}
