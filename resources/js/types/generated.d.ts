declare namespace App.Data.Basic {
export type BrandData = {
id: number;
name: string;
slug: string;
image: string;
productsCount: any | number;
};
export type CartData = {
id: number;
items: CartItemData[];
itemsCount: number;
selectedCount: number;
subtotal: string;
formattedSubtotal: string;
isAllSelected: boolean;
};
export type CartItemData = {
id: number;
quantity: number;
isSelected: boolean;
product: App.Data.Basic.SimpleProductData;
productVariant: App.Data.Basic.SimpleVariantData;
};
export type CategoryData = {
id: number;
name: string;
slug: string;
image: string;
productsCount: number;
children: CategoryData[];
};
export type ImageData = {
path: string;
altText: string | null;
displayOrder: number;
};
export type OrderDetailsData = {
orderId: number;
orderNumber: string;
status: number;
statusLabel: string;
paymentStatus: number;
paymentStatusLabel: string;
paymentMethodLabel: string;
trackingNumber: string | null;
createdAt: string;
expectedDelivery: string | null;
canReturn: boolean;
canCancel: boolean;
returnWindowEndsAt: string | null;
canReview: boolean;
shippingAddress: Array<any>;
canPay: boolean;
items: OrderDetailsItemData[];
};
export type OrderDetailsItemData = {
id: number;
productId: number | null;
name: string;
image: string | null;
attributes: Array<any>;
quantity: number;
price: string;
formattedPrice: string;
total: string;
formattedTotal: string;
productSlug: string | null;
review: Array<any> | null;
};
export type OrderItemPreviewData = {
id: number;
name: string;
image: string | null;
};
export type OrderListItemData = {
orderId: number;
orderNumber: string;
status: number;
statusLabel: string;
paymentStatus: number;
paymentStatusLabel: string;
itemsCount: number;
grandTotal: string;
formattedGrandTotal: string;
createdAt: string;
createdAtIso: string;
expectedDelivery: string;
shippingName: string;
trackingNumber: string | null;
itemsPreview: OrderItemPreviewData[];
moreItemsCount: number;
canPay: boolean;
searchText: string;
};
export type OrderSummaryData = {
formattedSubtotal: string;
formattedTaxAmount: string;
formattedShippingCost: string;
formattedDiscountAmount: string;
formattedGrandTotal: string;
};
export type ProductAttributeData = {
id: number;
name: string;
type: App.Enums.AttributeType;
selectedValue: AttributeValueData|null;
valuesCount: number;
values: AttributeValueData[];
};
export type ProductData = {
id: number;
name: string;
slug: string;
description: string | null;
rating: number;
reviews: number;
variantsCount: number;
featured: boolean;
defaultVariant: App.Data.Basic.ProductVariantData;
};
export type ProductDetailsData = {
id: number;
name: string;
slug: string;
description: string | null;
rating: number;
reviews: number;
variantsCount: number;
brand: App.Data.Basic.BrandData | null;
specifications: Array<{key: string, value: string}>;
attributes: ProductAttributeData[];
variant: App.Data.Basic.ProductVariantData;
featured: boolean;
isNew: boolean;
};
export type ProductReviewData = {
id: number;
userName: string;
userAvatar: string | null;
rating: number;
comment: string;
date: string;
created_at: any;
verified: boolean;
};
export type ProductReviewsSummaryData = {
averageRating: number;
totalReviews: number;
distribution: { [key: number]: number };
};
export type ProductVariantData = {
id: number;
sku: string;
price: string;
compareAtPrice: string | null;
discountPercent: number | null;
quantity: number;
isDefault: boolean;
defaultImage: ImageData[]|undefined;
images: ImageData[]|undefined;
};
export type ReturnDetailsData = {
returnId: number;
returnNumber: string;
status: number;
statusLabel: string;
createdAt: string;
orderNumber: string | null;
itemsCount: number;
refundAmount: string;
formattedRefundAmount: string;
refundMethodLabel: string | null;
trackingNumber: string | null;
shippingLabelUrl: string | null;
items: ReturnItemDetailsData[];
timeline: ReturnTimelineData[];
};
export type ReturnInspectionData = {
conditionLabel: string;
resolutionLabel: string;
quantity: number;
refundAmount: string | null;
formattedRefundAmount: string | null;
note: string | null;
};
export type ReturnItemDetailsData = {
id: number;
productName: string;
image: string | null;
attributes: Array<any>;
reason: string;
quantity: number;
unitPrice: string;
formattedUnitPrice: string;
total: string;
formattedTotal: string;
inspectionStatus: string;
inspections: ReturnInspectionData[];
};
export type ReturnSummaryData = {
returnId: number;
returnNumber: string;
orderNumber: string | null;
status: number;
statusLabel: string;
itemsCount: number;
refundAmount: string;
formattedRefundAmount: string;
createdAt: string;
};
export type ReturnTimelineData = {
status: number;
statusLabel: string;
comment: string | null;
createdAt: string;
};
export type SimpleProductData = {
id: number;
name: string;
slug: string;
};
export type SimpleVariantData = {
id: number;
price: string;
quantity: number;
defaultImage: App.Data.Basic.ImageData | null;
attributes: VariantAttributeData[];
};
export type VariantAttributeData = {
variantId: number;
attributeId: number;
attributeName: string;
attributeType: App.Enums.AttributeType;
valueId: number;
valueName: string;
colorCode: string | null;
};
export type WishlistData = {
items: WishlistItemData[];
itemsCount: number;
};
export type WishlistItemData = {
id: number;
product: App.Data.Basic.SimpleProductData;
productVariant: App.Data.Basic.SimpleVariantData;
};
}
declare namespace App.Data.Search {
export type SearchSuggestionData = {
id: number;
name: string;
slug: string;
type: string;
image: string | null;
price: string | null;
};
}
declare namespace App.Enums {
export enum AttributeType { Text = 1, Color = 2 };
export enum BrandStatus { Draft = 0, Published = 1, Archived = 2 };
export enum CancelRefundOption { AUTO = 'auto', MANUAL = 'manual', LATER = 'later' };
export enum CategoryStatus { Draft = 0, Published = 1, Archived = 2 };
export enum ItemCondition { SEALED = 1, OPEN_BOX = 2, DAMAGED = 3, WRONG_ITEM = 4 };
export enum OrderStatus { PENDING = 0, PROCESSING = 2, SHIPPED = 3, DELIVERED = 4, CANCELLED = 5, RETURNED = 6 };
export enum OrderType { NORMAL = 1, REPLACEMENT = 2, RETURN_SHIPMENT = 3 };
export enum PaymentMethod { PENDING = 0, CREDIT_CARD = 1, MADA = 2, APPLE_PAY = 3, BANK_TRANSFER = 4 };
export enum PaymentStatus { PENDING = 0, PAID = 1, FAILED = 2, REFUNDED = 3, REFUND_PENDING = 4, PARTIALLY_REFUNDED = 5 };
export enum ProductStatus { Draft = 0, Published = 1, Archived = 2 };
export enum RefundMethod { ORIGINAL = 1, WALLET = 2, BANK_TRANSFER = 3 };
export enum ReturnResolution { REFUND = 1, REPLACEMENT = 2, REJECT = 3 };
export enum ReturnStatus { REQUESTED = 1, APPROVED = 2, SHIPPED_BACK = 3, RECEIVED = 4, INSPECTED = 5, COMPLETED = 6, REJECTED = 7 };
export enum StockMovementType { SALE = 0, REPLACEMENT_OUT = 7, WASTE = 5, SUPPLIER_RESTOCK = 2, RETURN_RESTOCK = 1, ORDER_CANCELLATION = 4, ADJUSTMENT = 3, TRANSFER = 6 };
export enum TransactionStatus { Pending = 0, Success = 1, Failed = 2, Cancelled = 3 };
export enum TransactionType { Payment = 0, Refund = 1 };
}
