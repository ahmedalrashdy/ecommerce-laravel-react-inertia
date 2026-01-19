import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { useAddressForm } from '@/hooks/use-address-form';
import type { UserAddress } from '@/types/address';
import { Form } from '@inertiajs/react';

interface AddressFormProps {
    address?: UserAddress;
    action: string;
    method?: 'post' | 'put' | 'patch';
    onCancel?: () => void;
}
export function AddressForm({
    address,
    action,
    method = 'post',
    onCancel,
}: AddressFormProps) {
    const { formData, updateField } = useAddressForm({
        initialData: address,
    });

    return (
        <Form
            action={action}
            method={method}
            className="space-y-6"
        >
            {({ processing, errors }) => (
                <>
                    <div className="grid gap-6 md:grid-cols-2">
                        {/* Contact Person */}
                        <div className="grid gap-2">
                            <Label htmlFor="contact_person">
                                اسم جهة الاتصال *
                            </Label>
                            <Input
                                id="contact_person"
                                name="contact_person"
                                type="text"
                                value={formData.contact_person}
                                onChange={(e) =>
                                    updateField(
                                        'contact_person',
                                        e.target.value,
                                    )
                                }
                                required
                                placeholder="اسم الشخص المسؤول"
                            />
                            <InputError message={errors.contact_person} />
                        </div>

                        {/* Contact Phone */}
                        <div className="grid gap-2">
                            <Label htmlFor="contact_phone">رقم الهاتف *</Label>
                            <Input
                                id="contact_phone"
                                name="contact_phone"
                                type="tel"
                                value={formData.contact_phone}
                                onChange={(e) =>
                                    updateField('contact_phone', e.target.value)
                                }
                                required
                                placeholder="05xxxxxxxx"
                            />
                            <InputError message={errors.contact_phone} />
                        </div>
                    </div>

                    {/* Address Line 1 */}
                    <div className="grid gap-2">
                        <Label htmlFor="address_line_1">
                            عنوان السطر الأول *
                        </Label>
                        <Input
                            id="address_line_1"
                            name="address_line_1"
                            type="text"
                            value={formData.address_line_1}
                            onChange={(e) =>
                                updateField('address_line_1', e.target.value)
                            }
                            required
                            placeholder="عنوان السطر الأول"
                        />
                        <InputError message={errors.address_line_1} />
                    </div>

                    {/* Address Line 2 */}
                    <div className="grid gap-2">
                        <Label htmlFor="address_line_2">
                            عنوان السطر الثاني
                        </Label>
                        <Input
                            id="address_line_2"
                            name="address_line_2"
                            type="text"
                            value={formData.address_line_2 || ''}
                            onChange={(e) =>
                                updateField(
                                    'address_line_2',
                                    e.target.value || null,
                                )
                            }
                            placeholder="عنوان السطر الثاني (اختياري)"
                        />
                        <InputError message={errors.address_line_2} />
                    </div>

                    <div className="grid gap-6 md:grid-cols-2">
                        {/* City */}
                        <div className="grid gap-2">
                            <Label htmlFor="city">المدينة</Label>
                            <Input
                                id="city"
                                name="city"
                                type="text"
                                value={formData.city || ''}
                                onChange={(e) =>
                                    updateField('city', e.target.value || null)
                                }
                                placeholder="المدينة"
                            />
                            <InputError message={errors.city} />
                        </div>

                        {/* State */}
                        <div className="grid gap-2">
                            <Label htmlFor="state">المنطقة/المحافظة</Label>
                            <Input
                                id="state"
                                name="state"
                                type="text"
                                value={formData.state || ''}
                                onChange={(e) =>
                                    updateField('state', e.target.value || null)
                                }
                                placeholder="المنطقة/المحافظة"
                            />
                            <InputError message={errors.state} />
                        </div>

                        {/* Postal Code */}
                        <div className="grid gap-2">
                            <Label htmlFor="postal_code">الرمز البريدي</Label>
                            <Input
                                id="postal_code"
                                name="postal_code"
                                type="text"
                                value={formData.postal_code || ''}
                                onChange={(e) =>
                                    updateField(
                                        'postal_code',
                                        e.target.value || null,
                                    )
                                }
                                placeholder="الرمز البريدي"
                            />
                            <InputError message={errors.postal_code} />
                        </div>

                        {/* Country */}
                        <div className="grid gap-2">
                            <Label htmlFor="country">الدولة</Label>
                            <Input
                                id="country"
                                name="country"
                                type="text"
                                value={formData.country || ''}
                                onChange={(e) =>
                                    updateField(
                                        'country',
                                        e.target.value || null,
                                    )
                                }
                                placeholder="الدولة"
                            />
                            <InputError message={errors.country} />
                        </div>
                    </div>

                    {/* Form Actions */}
                    <div className="flex gap-4">
                        <Button
                            type="submit"
                            disabled={processing}
                            className="flex-1"
                        >
                            {processing && <Spinner />}
                            {address ? 'تحديث العنوان' : 'إضافة العنوان'}
                        </Button>
                        {onCancel && (
                            <Button
                                type="button"
                                variant="outline"
                                onClick={onCancel}
                                disabled={processing}
                            >
                                إلغاء
                            </Button>
                        )}
                    </div>
                </>
            )}
        </Form>
    );
}
