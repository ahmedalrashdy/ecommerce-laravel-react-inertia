import * as React from 'react';
import { CartDropdownSkeleton } from './skeletons/CartDropdownSkeleton';
import { CartItemSkeleton } from './skeletons/CartItemSkeleton';
import { CartPageSkeleton } from './skeletons/CartPageSkeleton';
import { CartSummarySkeleton } from './skeletons/CartSummarySkeleton';

type SkeletonName = 'cartItem' | 'cartDropdown' | 'cartPage' | 'cartSummary';

interface SkeletonWrapperProps {
    name: SkeletonName;
    className?: string;
}

export const SkeletonWrapper: React.FC<SkeletonWrapperProps> = ({
    name,
    className,
}) => {
    switch (name) {
        case 'cartItem':
            return <CartItemSkeleton className={className} />;
        case 'cartDropdown':
            return <CartDropdownSkeleton className={className} />;
        case 'cartPage':
            return <CartPageSkeleton className={className} />;
        case 'cartSummary':
            return <CartSummarySkeleton className={className} />;
        default:
            return null;
    }
};
