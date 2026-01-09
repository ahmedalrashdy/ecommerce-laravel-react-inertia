import { useEffect } from 'react';
import { router } from '@inertiajs/react';
import { toast } from 'sonner';

type FlashToast = {
    type: 'success' | 'info' | 'error' | 'warning';
    message: string;
};

export default function FlashMessages() {
    useEffect(() => {
        const remove = router.on('flash', (event) => {
            const t = (event.detail as any)?.flash?.toast as
                | FlashToast
                | undefined;
            if (!t) return;

            switch (t.type) {
                case 'success':
                    toast.success(t.message);
                    break;
                case 'error':
                    toast.error(t.message);
                    break;
                case 'info':
                    toast.message(t.message);
                    break;
                case 'warning':
                    toast.warning(t.message);
                    break;
            }
        });

        return remove;
    }, []);

    return null;
}
