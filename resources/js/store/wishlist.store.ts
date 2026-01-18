import { create } from "zustand";

type VariantId = number;

interface WishlistStore {
    variants: VariantId[];
    prevVariants: VariantId[];
    setVariantsIds: (variantsIds: VariantId[]) => void;
    inWishlist: (variantId: VariantId) => boolean;
    wasChanged: () => boolean;
}

export const useWishlistStore = create<WishlistStore>((set, get) => ({
    variants: [],
    prevVariants: [],

    setVariantsIds: (variantsIds) =>
        set((state) => ({
            prevVariants: state.variants,
            variants: variantsIds,
        })),

    inWishlist: (variantId) => {
        return get().variants.includes(variantId);
    },

    wasChanged: () => {
        const { variants, prevVariants } = get();

        if (variants.length !== prevVariants.length) {
            return true;
        }

        return !variants.every(
            (value, index) => value === prevVariants[index]
        );
    },
}));
