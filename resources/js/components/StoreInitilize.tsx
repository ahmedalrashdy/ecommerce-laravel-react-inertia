import { useCartStore } from '@/store/cart.store';
import { useWishlistStore } from '@/store/wishlist.store';
import { usePage } from '@inertiajs/react';
import { useEffect } from 'react';

export default function StoreInitilize() {
    const { wishlistVariantIds, cartVariantIds } = usePage<{
        wishlistVariantIds: number[];
        cartVariantIds: Record<number, number>;
    }>().props;

    const setItems = useCartStore((s) => s.setItems);
    const setVariantsIds = useWishlistStore((s) => s.setVariantsIds);

    useEffect(() => {
        setItems(cartVariantIds);
    }, [cartVariantIds, setItems]);

    useEffect(() => {
        setVariantsIds(wishlistVariantIds);
    }, [wishlistVariantIds, setVariantsIds]);

    return null;
}
