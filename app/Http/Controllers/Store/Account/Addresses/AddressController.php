<?php

namespace App\Http\Controllers\Store\Account\Addresses;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store\Addresses\StoreAddressRequest;
use App\Http\Requests\Store\Addresses\UpdateAddressRequest;
use App\Models\UserAddress;
use App\Traits\FlashMessage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AddressController extends Controller
{
    use AuthorizesRequests, FlashMessage;

    /**
     * Display a listing of the user's addresses.
     */
    public function index(Request $request): Response
    {
        $addresses = $request->user()
            ->userAddresses()
            ->orderByDesc('is_default_shipping')
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('store/account/addresses/index', [
            'addresses' => $addresses,
        ]);
    }

    /**
     * Show the form for creating a new address.
     */
    public function create(): Response
    {
        return Inertia::render('store/account/addresses/create');
    }

    /**
     * Store a newly created address.
     */
    public function store(StoreAddressRequest $request): RedirectResponse
    {
        $user = $request->user();
        $redirectTo = $request->input('redirect_to');

        // If this is set as default, unset other defaults
        if ($request->boolean('is_default_shipping')) {
            $user->userAddresses()->update(['is_default_shipping' => false]);
        }

        $user->userAddresses()->create($request->validated());

        $this->flashSuccess(__('تم إضافة العنوان بنجاح'));

        if (is_string($redirectTo) && Str::startsWith($redirectTo, '/')) {
            return redirect($redirectTo);
        }

        return redirect()->route('store.account.addresses.index');
    }

    /**
     * Update the specified address.
     */
    public function update(UpdateAddressRequest $request, UserAddress $address): RedirectResponse
    {
        $this->authorize('update', $address);

        if ($request->boolean('is_default_shipping')) {
            $request->user()
                ->userAddresses()
                ->where('id', '!=', $address->id)
                ->update(['is_default_shipping' => false]);
        }

        $address->update($request->validated());

        $this->flashSuccess(__('تم تحديث العنوان بنجاح'));

        return redirect()->route('store.account.addresses.index');
    }

    /**
     * Remove the specified address.
     */
    public function destroy(Request $request, UserAddress $address): RedirectResponse
    {
        $this->authorize('delete', $address);

        $address->delete();

        $this->flashSuccess(__('تم حذف العنوان بنجاح'));

        return redirect()->route('store.account.addresses.index');
    }

    /**
     * Set the specified address as default shipping address.
     */
    public function setDefaultShipping(Request $request, UserAddress $address): RedirectResponse
    {
        $this->authorize('update', $address);

        $request->user()
            ->userAddresses()
            ->where('id', '!=', $address->id)
            ->update(['is_default_shipping' => false]);

        $address->update(['is_default_shipping' => true]);

        $this->flashSuccess(__('تم تعيين العنوان كافتراضي للشحن'));

        return back();
    }
}
