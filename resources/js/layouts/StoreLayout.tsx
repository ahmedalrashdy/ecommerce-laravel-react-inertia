import FlashMessages from '@/components/common/flash-messages';
import { Footer } from '@/components/partials/footer';
import Header from '@/components/partials/Header/Header';
import StoreInitilize from '@/components/StoreInitilize';
import { Toaster } from '@/components/ui/sonner';
import { DirectionProvider } from '@radix-ui/react-direction';
import React from 'react';

export default function StoreLayout({
    children,
}: {
    children: React.ReactNode;
}) {
    return (
        <DirectionProvider dir="rtl">
            <div className="min-h-screen bg-background font-sans antialiased">
                <Toaster richColors />
                <FlashMessages />
                <StoreInitilize />
                <Header />

                <main className="flex-1">{children}</main>

                <Footer />
            </div>
        </DirectionProvider>
    );
}
