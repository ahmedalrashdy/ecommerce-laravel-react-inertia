import { create } from "zustand";

type VariantId = number;

interface CartStore {
    items: Record<VariantId, number>;
    prevItems: Record<VariantId, number>;

    setItems: (items: Record<VariantId, number>) => void;
    inCart: (variantId: VariantId) => boolean;
    getQuantity: (variantId: VariantId) => number;
    count: () => number;
    wasChanged: () => boolean;
}

export const useCartStore = create<CartStore>((set, get) => ({
    items: {},
    prevItems: {},

    setItems: (items) =>
        set((state) => ({
            prevItems: state.items,
            items,
        })),

    inCart: (variantId) => variantId in get().items,

    getQuantity: (variantId) => get().items[variantId] ?? 1,

    count: () => Object.keys(get().items).length,

    wasChanged: () => {
        const { items, prevItems } = get();

        const keys = Object.keys(items);
        const prevKeys = Object.keys(prevItems);

        // 1️⃣ اختلاف عدد العناصر
        if (keys.length !== prevKeys.length) {
            return true;
        }

        // 2️⃣ اختلاف المفتاح أو الكمية
        for (const key of keys) {
            const id = Number(key);

            if (!(id in prevItems)) {
                return true; // مفتاح جديد
            }

            if (items[id] !== prevItems[id]) {
                return true; // نفس المفتاح لكن كمية مختلفة
            }
        }

        return false;
    },
}));
