import StoreLayout from '@/layouts/StoreLayout';
import type { ReactNode } from 'react';
import AccountSidebar from './AccountSidebar';

interface AccountLayoutProps {
    children: ReactNode;
}

export default function AccountLayout({ children }: AccountLayoutProps) {
    return (
        <StoreLayout>
            <div className="container mx-auto px-4 py-8 lg:py-12">
                <div className="flex flex-col gap-6 lg:flex-row lg:items-start">
                    <div className="order-1 flex-1">{children}</div>
                    <div className="order-2 lg:w-[280px]">
                        <AccountSidebar />
                    </div>
                </div>
            </div>
        </StoreLayout>
    );
}
