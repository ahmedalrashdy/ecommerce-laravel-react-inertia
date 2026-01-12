import { useMutation } from '@tanstack/react-query';
import * as React from 'react';
import api from '@/lib/axios';

interface SubscribeResponse {
    message: string;
}

interface UseNewsletterSubscriptionOptions {
    onSuccess?: () => void;
    resetAfterSuccess?: boolean;
    resetDelay?: number;
}

export const useNewsletterSubscription = (
    options: UseNewsletterSubscriptionOptions = {},
) => {
    const { onSuccess, resetAfterSuccess = true, resetDelay = 5000 } =
        options;
    const [email, setEmail] = React.useState('');
    const [successMessage, setSuccessMessage] = React.useState<string | null>(
        null,
    );

    const subscribeMutation = useMutation({
        mutationFn: async (emailValue: string): Promise<SubscribeResponse> => {
            const response = await api.post<SubscribeResponse>(
                '/store/newsletter/subscribe',
                { email: emailValue },
            );
            return response.data;
        },
        onSuccess: (data) => {
            setSuccessMessage(data.message);
            if (resetAfterSuccess) {
                setEmail('');
                // Reset success state after delay
                setTimeout(() => {
                    subscribeMutation.reset();
                    setSuccessMessage(null);
                }, resetDelay);
            }
            onSuccess?.();
        },
    });

    const handleSubmit = React.useCallback(
        (e: React.FormEvent) => {
            e.preventDefault();
            if (email.trim()) {
                subscribeMutation.mutate(email);
            }
        },
        [email, subscribeMutation],
    );

    const error = subscribeMutation.error as {
        response?: {
            data?: {
                message?: string;
                errors?: {
                    email?: string[];
                };
            };
        };
    } | null;

    // Get error message from Laravel validation response
    const errorMessage =
        error?.response?.data?.message ||
        error?.response?.data?.errors?.email?.[0] ||
        null;

    return {
        email,
        setEmail,
        handleSubmit,
        isLoading: subscribeMutation.isPending,
        isSuccess: subscribeMutation.isSuccess,
        successMessage,
        errorMessage,
        reset: () => {
            subscribeMutation.reset();
            setSuccessMessage(null);
        },
    };
};
