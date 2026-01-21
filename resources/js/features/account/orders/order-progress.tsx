import { cn } from '@/lib/utils';
import { Check } from 'lucide-react';

type OrderProgressProps = {
    status: number;
    statusLabel: string;
};

const steps = [
    { key: 'placed', label: 'تم الطلب' },
    { key: 'processing', label: 'تم التجهيز' },
    { key: 'shipped', label: 'تم الشحن' },
    { key: 'delivered', label: 'تم التوصيل' },
];

const statusToStep: Record<number, number> = {
    0: 1,
    1: 2,
    2: 2,
    3: 3,
    4: 4,
    6: 4,
};

export function OrderProgress({ status, statusLabel }: OrderProgressProps) {
    const currentStep = statusToStep[status] ?? 1;
    const isCancelled = status === 5;

    return (
        <div className="rounded-3xl border border-border/60 bg-card/80 p-6 shadow-sm">
            <div className="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div className="grid gap-1">
                    <p className="text-sm text-muted-foreground">
                        حالة الطلب الحالية
                    </p>
                    <p
                        className={cn(
                            'text-xl font-semibold',
                            isCancelled ? 'text-rose-600' : 'text-foreground',
                        )}
                    >
                        {statusLabel}
                    </p>
                </div>
                <div className="flex flex-wrap items-center gap-2 text-xs text-muted-foreground">
                    <span className="rounded-full border border-border/60 bg-muted/40 px-3 py-1">
                        الخطوة {currentStep} من {steps.length}
                    </span>
                    {isCancelled && (
                        <span className="rounded-full border border-rose-200 bg-rose-50 px-3 py-1 font-semibold text-rose-700">
                            تم إلغاء الطلب
                        </span>
                    )}
                </div>
            </div>

            <div className="mt-6 flex flex-wrap items-center gap-4">
                {steps.map((step, index) => {
                    const stepNumber = index + 1;
                    const isCompleted = stepNumber < currentStep;
                    const isActive = stepNumber === currentStep;

                    return (
                        <div
                            key={step.key}
                            className="flex flex-1 items-center gap-3"
                        >
                            <div className="flex flex-col items-center gap-2">
                                <span
                                    className={cn(
                                        'flex h-11 w-11 items-center justify-center rounded-full border text-sm font-semibold',
                                        isCompleted
                                            ? 'border-primary bg-primary text-primary-foreground shadow-sm shadow-primary/20'
                                            : isActive
                                              ? 'border-primary text-primary'
                                              : 'border-muted-foreground/30 text-muted-foreground',
                                    )}
                                >
                                    {isCompleted ? (
                                        <Check className="h-4 w-4" />
                                    ) : (
                                        stepNumber
                                    )}
                                </span>
                                <span
                                    className={cn(
                                        'text-xs font-medium',
                                        isCompleted || isActive
                                            ? 'text-foreground'
                                            : 'text-muted-foreground',
                                    )}
                                >
                                    {step.label}
                                </span>
                            </div>
                            {index < steps.length - 1 && (
                                <div
                                    className={cn(
                                        'h-1 flex-1 rounded-full',
                                        isCompleted
                                            ? 'bg-gradient-to-r from-primary to-primary/40'
                                            : 'bg-muted',
                                    )}
                                />
                            )}
                        </div>
                    );
                })}
            </div>
        </div>
    );
}
