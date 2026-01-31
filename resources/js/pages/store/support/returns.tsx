import { Button } from '@/components/ui/button';
import StoreLayout from '@/layouts/StoreLayout';
import {
    Calendar,
    Circle,
    CreditCard,
    FileText,
    Mail,
    MessageCircle,
    Minus,
    Plus,
    RotateCcw,
    Search,
    Shield,
    Truck,
} from 'lucide-react';
import React, { useMemo, useState } from 'react';
import { Head, Link } from '@inertiajs/react';

const brand = {
    nameAr: 'زين ماركت',
    nameEn: 'Zain Market',
    supportEmail: 'support@zain.market',
    returnsEmail: 'returns@zain.market',
    whatsapp: '+966551239876',
    whatsappDisplay: '+966 55 123 9876',
    location: 'الرياض – المملكة العربية السعودية',
    lastUpdated: '2026-01-01',
};

const assets = {
    heroBg: 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=2400&q=80',
};

const policy = {
    windowDays: 7,
    sealedRequired: true,
    accessoriesRequired: true,
    inspectionDays: '3–5 أيام عمل',
    refundSla: '5–10 أيام عمل (قد تختلف حسب البنك/مزود الدفع)',
    exchangeAllowed: true,
    exchangeWindowDays: 7,
    shippingFees: {
        defective: 'نتحمل تكاليف الشحن العكسي عند ثبوت عيب مصنعي أو خطأ في التوريد.',
        changeMind:
            'قد يتحمل العميل تكاليف الشحن العكسي في حالات الإرجاع بسبب تغيير الرغبة/اختيار غير مناسب.',
    },
};

function formatDate(iso: string): string {
    const [y, m, d] = String(iso || '').split('-');
    if (!y || !m || !d) {
        return iso;
    }
    return `${y}/${m}/${d}`;
}

function Kpi({
    label,
    value,
    hint,
    icon: Icon,
}: {
    label: string;
    value: string;
    hint: string;
    icon: React.ComponentType<{ className?: string }>;
}) {
    return (
        <div className="rounded-xl border border-border bg-card p-4 shadow-sm">
            <div className="flex items-start gap-3">
                <span className="grid size-10 shrink-0 place-items-center rounded-lg bg-primary text-primary-foreground">
                    <Icon className="size-5" />
                </span>
                <div className="min-w-0 flex-1">
                    <p className="text-xs font-medium text-muted-foreground">
                        {label}
                    </p>
                    <p className="mt-1 text-sm font-bold text-foreground">
                        {value}
                    </p>
                    <p className="mt-1 text-xs text-muted-foreground">{hint}</p>
                </div>
            </div>
        </div>
    );
}

function Bullet({
    title,
    desc,
}: {
    title: string;
    desc: string;
}) {
    return (
        <div className="flex items-start gap-3 rounded-xl bg-muted/50 p-4 ring-1 ring-border">
            <span className="mt-0.5 grid size-9 shrink-0 place-items-center rounded-lg bg-primary text-primary-foreground">
                <Circle className="size-4" />
            </span>
            <div className="min-w-0 flex-1">
                <p className="text-sm font-bold text-foreground">{title}</p>
                <p className="mt-1 text-sm leading-7 text-muted-foreground">
                    {desc}
                </p>
            </div>
        </div>
    );
}

function Step({
    n,
    title,
    desc,
    icon: Icon,
}: {
    n: string;
    title: string;
    desc: string;
    icon: React.ComponentType<{ className?: string }>;
}) {
    return (
        <div className="rounded-xl border border-border bg-card p-5 shadow-sm">
            <div className="flex items-start gap-4">
                <span className="grid size-11 shrink-0 place-items-center rounded-lg bg-primary text-primary-foreground">
                    <Icon className="size-5" />
                </span>
                <div className="min-w-0 flex-1">
                    <p className="text-xs font-bold text-muted-foreground">
                        الخطوة {n}
                    </p>
                    <p className="mt-1 text-sm font-bold text-foreground">
                        {title}
                    </p>
                    <p className="mt-2 text-sm leading-7 text-muted-foreground">
                        {desc}
                    </p>
                </div>
            </div>
        </div>
    );
}

function ContactCard({
    title,
    value,
    href,
    icon: Icon,
    external,
}: {
    title: string;
    value: string;
    href: string;
    icon: React.ComponentType<{ className?: string }>;
    external?: boolean;
}) {
    const Inner = (
        <div className="flex items-start gap-3">
            <span className="grid size-10 shrink-0 place-items-center rounded-lg bg-primary text-primary-foreground">
                <Icon className="size-5" />
            </span>
            <div className="min-w-0 flex-1">
                <p className="text-sm font-bold text-foreground">{title}</p>
                <p className="mt-1 text-sm text-muted-foreground">{value}</p>
            </div>
        </div>
    );

    return external ? (
        <a
            href={href}
            target="_blank"
            rel="noreferrer"
            className="rounded-xl border border-border bg-card p-4 shadow-sm ring-1 ring-border transition-all hover:bg-muted/50 focus:outline-none focus:ring-2 focus:ring-ring"
        >
            {Inner}
        </a>
    ) : (
        <a
            href={href}
            className="rounded-xl border border-border bg-card p-4 shadow-sm ring-1 ring-border transition-all hover:bg-muted/50 focus:outline-none focus:ring-2 focus:ring-ring"
        >
            {Inner}
        </a>
    );
}

function FaqBlock() {
    const faqs = [
        {
            id: 'r-faq-1',
            q: 'هل يمكنني الإرجاع بسبب تغيير الرغبة؟',
            a: 'نعم، بشرط الالتزام بالمدة وحالة المنتج والملحقات. قد يتحمل العميل رسوم الشحن العكسي في هذه الحالة.',
        },
        {
            id: 'r-faq-2',
            q: 'متى يتم رد المبلغ؟',
            a: 'بعد استلام المرتجع وفحصه واعتماد الاسترداد. قد يحتاج مزود الدفع/البنك وقتاً إضافياً لظهور المبلغ.',
        },
        {
            id: 'r-faq-3',
            q: 'هل يمكنني استبدال المتغير (لون/سعة)؟',
            a: 'عادةً نعم وفق توفر المخزون ونتيجة الفحص. قد يطبق فرق السعر إن وجد.',
        },
        {
            id: 'r-faq-4',
            q: 'ماذا لو كان المنتج به عيب مصنعي؟',
            a: 'نرجو توثيق المشكلة بالصور/الفيديو والتواصل فوراً. عند ثبوت العيب، تتم المعالجة بأولوية وغالباً نتحمل الشحن العكسي.',
        },
    ];

    const [open, setOpen] = useState<string | null>(faqs[0]?.id ?? null);

    return (
        <div className="space-y-3">
            {faqs.map((f) => (
                <div
                    key={f.id}
                    className="rounded-xl border border-border bg-background"
                >
                    <button
                        type="button"
                        onClick={() => setOpen((p) => (p === f.id ? null : f.id))}
                        className="flex w-full items-start justify-between gap-4 rounded-xl px-4 py-4 text-right focus:outline-none focus:ring-2 focus:ring-ring"
                        aria-expanded={open === f.id}
                    >
                        <span className="text-sm font-bold text-foreground">
                            {f.q}
                        </span>
                        <span className="grid size-8 shrink-0 place-items-center rounded-lg bg-muted/50 ring-1 ring-border">
                            {open === f.id ? (
                                <Minus className="size-4 text-foreground" />
                            ) : (
                                <Plus className="size-4 text-foreground" />
                            )}
                        </span>
                    </button>
                    {open === f.id ? (
                        <div className="px-4 pb-4">
                            <div className="rounded-xl bg-muted/50 p-4 ring-1 ring-border">
                                <p className="text-sm leading-7 text-muted-foreground">
                                    {f.a}
                                </p>
                            </div>
                        </div>
                    ) : null}
                </div>
            ))}
        </div>
    );
}

export default function ReturnsPage() {
    const sections = useMemo(
        () => [
        {
            id: 'overview',
            title: 'نظرة عامة',
            content: (
                <div className="space-y-3">
                    <p className="text-sm leading-7 text-muted-foreground">
                        تهدف هذه السياسة إلى توضيح شروط وآلية الإرجاع والاستبدال لدى{' '}
                        <span className="font-semibold text-foreground">
                            {brand.nameAr}
                        </span>
                        . نرجو قراءة البنود بعناية قبل تقديم طلب الإرجاع أو الاستبدال.
                    </p>
                    <div className="grid gap-3 sm:grid-cols-3">
                        <Kpi
                            label="مدة تقديم طلب الإرجاع"
                            value={`${policy.windowDays} أيام`}
                            hint="من تاريخ الاستلام"
                            icon={Calendar}
                        />
                        <Kpi
                            label="مدة فحص المرتجع"
                            value={policy.inspectionDays}
                            hint="بعد الاستلام"
                            icon={Search}
                        />
                        <Kpi
                            label="مدة الاسترداد المالي"
                            value={policy.refundSla}
                            hint="بعد اعتماد الفحص"
                            icon={CreditCard}
                        />
                    </div>
                </div>
            ),
        },
        {
            id: 'eligibility',
            title: 'شروط القبول',
            content: (
                <div className="space-y-3">
                    <Bullet
                        title="تقديم الطلب خلال المدة"
                        desc={`يجب تقديم طلب الإرجاع خلال ${policy.windowDays} أيام من تاريخ الاستلام.`}
                    />
                    <Bullet
                        title="حالة المنتج"
                        desc={
                            policy.sealedRequired
                                ? 'يجب أن يكون المنتج بحالته الأصلية قدر الإمكان، مع التغليف والملحقات.'
                                : 'يجب أن يكون المنتج بحالته الأصلية قدر الإمكان.'
                        }
                    />
                    <Bullet
                        title="الملحقات والفواتير"
                        desc={
                            policy.accessoriesRequired
                                ? 'إرفاق جميع الملحقات، والكتيبات، والتغليف الأصلي. وجود رقم الطلب/الفاتورة يساعد على تسريع الإجراء.'
                                : 'وجود رقم الطلب/الفاتورة يساعد على تسريع الإجراء.'
                        }
                    />
                    <Bullet
                        title="التحقق والفحص"
                        desc="قد يُطلب صور أو معلومات إضافية قبل قبول الطلب، ويتم فحص المنتج عند استلامه."
                    />

                    <div className="rounded-xl bg-muted/50 p-4 ring-1 ring-border">
                        <p className="text-sm font-bold text-foreground">
                            ملاحظة تشغيلية
                        </p>
                        <p className="mt-2 text-sm leading-7 text-muted-foreground">
                            في حال وجود عيب مصنعي واضح أو خطأ توريد (منتج/متغير غير
                            مطابق)، يتم إعطاء أولوية للمعالجة وتحمّل تكاليف الشحن العكسي
                            وفق التحقق.
                        </p>
                    </div>
                </div>
            ),
        },
        {
            id: 'non-eligible',
            title: 'حالات غير مشمولة غالباً',
            content: (
                <div className="space-y-3">
                    <Bullet
                        title="سوء الاستخدام أو التلف"
                        desc="أي تلف ناتج عن سوء استخدام، كسر، سوائل، أو تعديل غير مصرح به."
                    />
                    <Bullet
                        title="نقص الملحقات"
                        desc="غياب الملحقات الأساسية أو التغليف بشكل يمنع إعادة البيع/إعادة التخزين."
                    />
                    <Bullet
                        title="تجاوز مدة الإرجاع"
                        desc={`طلبات الإرجاع بعد ${policy.windowDays} أيام من تاريخ الاستلام قد لا تُقبل.`}
                    />
                    <Bullet
                        title="منتجات ذات طبيعة خاصة"
                        desc="قد تستثنى بعض المنتجات بحسب طبيعتها أو اشتراطات المورد/المصنع (إن وجد ذلك، يُوضح في صفحة المنتج)."
                    />
                    <p className="text-sm leading-7 text-muted-foreground">
                        يتم تقييم كل حالة على حدة. في حال وجود تعارض بين هذه السياسة
                        وشروط المنتج المحددة في صفحة المنتج، تُطبّق الشروط الأكثر تحديداً
                        لذلك المنتج.
                    </p>
                </div>
            ),
        },
        {
            id: 'exchange',
            title: 'الاستبدال',
            content: (
                <div className="space-y-3">
                    <p className="text-sm leading-7 text-muted-foreground">
                        {policy.exchangeAllowed
                            ? `الاستبدال متاح عادةً خلال ${policy.exchangeWindowDays} أيام من الاستلام، وفق توفر المخزون وحالة المنتج.`
                            : 'الاستبدال غير متاح حالياً، ويمكن تقديم طلب إرجاع وفق الشروط.'}
                    </p>
                    <Bullet
                        title="ما الذي يمكن استبداله؟"
                        desc="استبدال نفس المنتج/المتغير أو متغير آخر حسب توفره ووفق نتيجة الفحص."
                    />
                    <Bullet
                        title="فرق السعر"
                        desc="إذا كان هناك فرق سعر بين المنتج المستبدَل والجديد، يتم احتسابه قبل الشحن."
                    />
                    <Bullet
                        title="تجهيز الاستبدال"
                        desc="يبدأ تجهيز الاستبدال بعد استلام المرتجع وفحصه واعتماد القرار."
                    />
                    <div className="rounded-xl bg-muted/50 p-4 ring-1 ring-border">
                        <p className="text-sm font-bold text-foreground">الشحن</p>
                        <p className="mt-2 text-sm leading-7 text-muted-foreground">
                            {policy.shippingFees.defective}
                        </p>
                        <p className="mt-2 text-sm leading-7 text-muted-foreground">
                            {policy.shippingFees.changeMind}
                        </p>
                    </div>
                </div>
            ),
        },
        {
            id: 'process',
            title: 'خطوات تقديم طلب الإرجاع/الاستبدال',
            content: (
                <div className="space-y-4">
                    <Step
                        n="1"
                        title="تقديم الطلب"
                        desc={`قدّم طلب الإرجاع من صفحة الإرجاع/الاستبدال (أو عبر الدعم) خلال ${policy.windowDays} أيام من الاستلام، مع توضيح السبب وإرفاق صور عند الحاجة.`}
                        icon={FileText}
                    />
                    <Step
                        n="2"
                        title="مراجعة أولية"
                        desc="نراجع الطلب وقد نطلب تفاصيل إضافية. إذا تم قبوله مبدئياً، سيتم تزويدك بتعليمات الإرجاع."
                        icon={Search}
                    />
                    <Step
                        n="3"
                        title="الشحن العكسي"
                        desc="قم بتغليف المنتج مع الملحقات كاملة. اتبع تعليمات الشحن العكسي وتحقق من حفظ رقم التتبع."
                        icon={Truck}
                    />
                    <Step
                        n="4"
                        title="الفحص والقرار"
                        desc={`بعد استلام المرتجع، يتم فحصه خلال ${policy.inspectionDays} وتحديد القرار: استرداد، استبدال، أو رفض (مع توضيح السبب).`}
                        icon={Shield}
                    />
                    <Step
                        n="5"
                        title="الاسترداد/الاستبدال"
                        desc={`عند اعتماد الاسترداد، يبدأ إجراء رد المبلغ خلال ${policy.refundSla}. وعند الاستبدال يتم تجهيز الطلب الجديد بعد اعتماد الفحص وتوفر المخزون.`}
                        icon={CreditCard}
                    />
                </div>
            ),
        },
        {
            id: 'refunds',
            title: 'الاسترداد المالي',
            content: (
                <div className="space-y-3">
                    <Bullet
                        title="طريقة الاسترداد"
                        desc="يتم الاسترداد عادةً عبر نفس وسيلة الدفع المستخدمة (إن أمكن) أو وفق ما يتطلبه مزود الدفع."
                    />
                    <Bullet
                        title="الخصومات والرسوم"
                        desc="قد تخصم رسوم الشحن أو رسوم الخدمة في حالات الإرجاع بسبب تغيير الرغبة حسب السياسة وحالة الطلب."
                    />
                    <Bullet
                        title="التأخير البنكي"
                        desc="بعد تنفيذ الاسترداد من طرفنا، قد يحتاج البنك/مزود الدفع وقتاً إضافياً لظهور المبلغ في حسابك."
                    />
                    <div className="rounded-xl bg-muted/50 p-4 ring-1 ring-border">
                        <p className="text-sm font-bold text-foreground">
                            متى يبدأ احتساب المدة؟
                        </p>
                        <p className="mt-2 text-sm leading-7 text-muted-foreground">
                            تبدأ مدة الاسترداد بعد اعتماد نتيجة الفحص (وليس من تاريخ
                            تقديم الطلب).
                        </p>
                    </div>
                </div>
            ),
        },
        {
            id: 'tips',
            title: 'نصائح لتسريع المعالجة',
            content: (
                <div className="space-y-3">
                    <Bullet
                        title="أرفق رقم الطلب"
                        desc="رقم الطلب يساعد على ربط البيانات بسرعة وتقليل الوقت."
                    />
                    <Bullet
                        title="صور واضحة"
                        desc="في حال وجود مشكلة (عيب/كسر/عدم مطابقة)، أرفق صور للمنتج والملصق والتغليف."
                    />
                    <Bullet
                        title="تغليف آمن"
                        desc="استخدم تغليفاً يحمي المنتج أثناء الشحن العكسي لتجنب رفض الطلب بسبب تلف أثناء الإرجاع."
                    />
                    <Bullet
                        title="احتفظ برقم التتبع"
                        desc="رقم التتبع يساعد على المتابعة الدقيقة مع شركة الشحن."
                    />
                </div>
            ),
        },
        {
            id: 'faq',
            title: 'أسئلة متكررة',
            content: <FaqBlock />,
        },
        {
            id: 'contact',
            title: 'التواصل',
            content: (
                <div className="space-y-3 text-sm leading-7 text-muted-foreground">
                    <p>لأي استفسار حول الإرجاع والاستبدال، تواصل معنا عبر:</p>
                    <div className="grid gap-3 sm:grid-cols-2">
                        <ContactCard
                            title="البريد (الإرجاع)"
                            value={brand.returnsEmail}
                            href={`mailto:${brand.returnsEmail}`}
                            icon={Mail}
                        />
                        <ContactCard
                            title="واتساب"
                            value={brand.whatsappDisplay}
                            href={`https://wa.me/${brand.whatsapp.replace('+', '')}`}
                            icon={MessageCircle}
                            external
                        />
                    </div>
                    <p className="text-xs text-muted-foreground">
                        آخر تحديث: {formatDate(brand.lastUpdated)} •{' '}
                        {brand.location}
                    </p>
                </div>
            ),
        },
        ],
        [brand, policy]
    );
    return (
        <StoreLayout>
            <Head title={`سياسة الإرجاع والاستبدال | ${brand.nameAr}`}>
                <meta
                    name="description"
                    content="سياسة الإرجاع والاستبدال الخاصة بزين ماركت: الشروط، المدد، الحالات المقبولة وغير المقبولة، خطوات التقديم، والاسترداد المالي."
                />
            </Head>

            {/* HERO */}
            <section className="relative overflow-hidden">
                <div className="absolute inset-0 z-0">
                    <img
                        src={assets.heroBg}
                        alt="خلفية سياسة الإرجاع والاستبدال"
                        className="h-full w-full object-cover"
                        loading="eager"
                        fetchPriority='high'
                        decoding='async'
                    />
                    <div className="absolute inset-0 bg-linear-to-b from-black/70 via-black/50 to-transparent" />
                </div>

                <div className="container relative z-10 mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="py-16 sm:py-24 lg:py-32">
                        <div className="max-w-2xl">
                            {/* Brand Mark */}
                            <div className="inline-flex items-center gap-3">
                                <div className="grid size-12 place-items-center rounded-2xl bg-white/15 ring-2 ring-white/30 backdrop-blur-md transition-all hover:scale-110 hover:bg-white/20">
                                    <RotateCcw className="size-6 text-white" />
                                </div>
                                <div className="leading-tight">
                                    <p className="text-sm font-bold text-white">
                                        {brand.nameAr}
                                    </p>
                                    <p className="text-xs font-medium text-white/90">
                                        {brand.nameEn}
                                    </p>
                                </div>
                            </div>

                            <h1 className="mt-8 text-4xl font-bold tracking-tight text-white sm:text-5xl lg:text-6xl">
                                سياسة الإرجاع والاستبدال
                            </h1>

                            <p className="mt-6 text-lg leading-8 text-white/95 sm:text-xl">
                                شروط واضحة وخطوات عملية لتقديم طلب إرجاع أو استبدال، مع
                                فحص واعتماد قبل الاسترداد أو إعادة الشحن.
                            </p>

                            <div className="mt-10 flex flex-wrap gap-3">
                                <span className="rounded-full bg-white/15 px-4 py-2 text-sm font-medium text-white ring-1 ring-white/30 backdrop-blur-md transition-all hover:scale-105 hover:bg-white/25">
                                    آخر تحديث: {formatDate(brand.lastUpdated)}
                                </span>
                                <Link
                                    href="/help"
                                    className="rounded-full bg-white/15 px-4 py-2 text-sm font-medium text-white ring-1 ring-white/30 backdrop-blur-md transition-all hover:scale-105 hover:bg-white/25"
                                >
                                    الأسئلة الشائعة
                                </Link>
                                <Link
                                    href="/terms"
                                    className="rounded-full bg-white/15 px-4 py-2 text-sm font-medium text-white ring-1 ring-white/30 backdrop-blur-md transition-all hover:scale-105 hover:bg-white/25"
                                >
                                    الشروط والأحكام
                                </Link>
                                <Link
                                    href="/contact"
                                    className="rounded-full bg-white/15 px-4 py-2 text-sm font-medium text-white ring-1 ring-white/30 backdrop-blur-md transition-all hover:scale-105 hover:bg-white/25"
                                >
                                    اتصل بنا
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* BODY */}
            <section className="container relative z-10 mx-auto -mt-8 px-4 py-10 sm:px-6 sm:py-14 lg:px-8">
                <div className="grid gap-8 lg:grid-cols-12 lg:items-start">
                    {/* TOC */}
                    <aside className="lg:col-span-4">
                        <div className="sticky top-6 rounded-2xl border border-border bg-card p-5 shadow-sm">
                            <p className="text-sm font-bold text-foreground">
                                المحتويات
                            </p>
                            <nav className="mt-4 space-y-1">
                                {sections.map((s) => (
                                    <a
                                        key={s.id}
                                        href={`#${s.id}`}
                                        className="block rounded-xl px-3 py-2 text-sm text-muted-foreground transition-all hover:bg-muted/50 hover:text-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                                    >
                                        {s.title}
                                    </a>
                                ))}
                            </nav>

                            <div className="mt-5 rounded-xl bg-muted/50 p-4 ring-1 ring-border">
                                <p className="text-xs font-bold text-foreground">
                                    روابط مهمة
                                </p>
                                <div className="mt-3 flex flex-wrap gap-2">
                                    <Link
                                        href="/shipping"
                                        className="rounded-full bg-background px-3 py-1 text-xs font-medium text-foreground ring-1 ring-border transition-all hover:bg-muted/50"
                                    >
                                        سياسة الشحن
                                    </Link>
                                    <Link
                                        href="/warranty"
                                        className="rounded-full bg-background px-3 py-1 text-xs font-medium text-foreground ring-1 ring-border transition-all hover:bg-muted/50"
                                    >
                                        سياسة الضمان
                                    </Link>
                                    <Link
                                        href="/privacy"
                                        className="rounded-full bg-background px-3 py-1 text-xs font-medium text-foreground ring-1 ring-border transition-all hover:bg-muted/50"
                                    >
                                        الخصوصية
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </aside>

                    {/* CONTENT */}
                    <div className="lg:col-span-8">
                        <div className="rounded-2xl border border-border bg-card p-6 shadow-sm">
                            {sections.map((s, idx) => (
                                <div key={s.id} id={s.id} className="scroll-mt-24">
                                    <h2 className="text-xl font-bold tracking-tight text-foreground sm:text-2xl">
                                        {s.title}
                                    </h2>
                                    <div className="mt-3">{s.content}</div>

                                    {idx !== sections.length - 1 ? (
                                        <div className="my-8 h-px bg-border" />
                                    ) : null}
                                </div>
                            ))}
                        </div>

                        {/* Bottom CTA */}
                        <div className="mt-6 rounded-xl bg-muted/50 p-6 ring-1 ring-border">
                            <div className="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <p className="text-sm font-bold text-foreground">
                                        تريد تقديم طلب إرجاع الآن؟
                                    </p>
                                    <p className="mt-1 text-sm text-muted-foreground">
                                        يمكنك البدء من صفحة حسابك أو التواصل معنا مع رقم
                                        الطلب.
                                    </p>
                                </div>
                                <div className="flex flex-wrap gap-2">
                                    <Button
                                        asChild
                                        size="sm"
                                        variant="default"
                                    >
                                        <Link href="/account/orders">طلباتي</Link>
                                    </Button>
                                    <Button
                                        asChild
                                        size="sm"
                                        variant="outline"
                                    >
                                        <Link href="/contact">اتصل بنا</Link>
                                    </Button>
                                </div>
                            </div>
                        </div>

                        <p className="mt-6 text-center text-xs text-muted-foreground">
                            © {new Date().getFullYear()} {brand.nameAr}. جميع
                            الحقوق محفوظة.
                        </p>
                    </div>
                </div>
            </section>
        </StoreLayout>
    );
}
