import { Button } from '@/components/ui/button';
import {
    ContactBadge,
    FooterCTA,
    HeroSection,
    InfoBox,
} from '@/features/support-and-legal/components';
import {
    ASSETS,
    BRAND_DATA,
} from '@/features/support-and-legal/constants/brand-data';
import StoreLayout from '@/layouts/StoreLayout';
import { Head, Link } from '@inertiajs/react';
import {
    Clock,
    Mail,
    MapPin,
    MessageCircle,
    Phone,
    Shield,
} from 'lucide-react';
import React, { useMemo, useState } from 'react';

const contactCards = [
    {
        title: 'الدعم عبر البريد',
        desc: 'للاستفسارات العامة ومتابعة الطلبات.',
        value: BRAND_DATA.email,
        href: `mailto:${BRAND_DATA.email}`,
        icon: Mail,
    },
    {
        title: 'واتساب',
        desc: 'للرد السريع ومساعدة ما قبل الشراء.',
        value: BRAND_DATA.whatsappDisplay,
        href: `https://wa.me/${BRAND_DATA.whatsapp.replace('+', '')}`,
        icon: MessageCircle,
        external: true,
    },
    {
        title: 'الهاتف',
        desc: 'خلال ساعات العمل الرسمية.',
        value: BRAND_DATA.phoneDisplay,
        href: `tel:${BRAND_DATA.phoneDisplay.replace(/\s/g, '')}`,
        icon: Phone,
    },
    {
        title: 'المقر',
        desc: 'مركز العمليات والتجهيز في الرياض.',
        value: BRAND_DATA.location,
        href: '#map',
        icon: MapPin,
    },
];

const policies = [
    { title: 'الشحن والتسليم', href: '/shipping' },
    { title: 'الإرجاع والاستبدال', href: '/returns' },
    { title: 'الضمان', href: '/warranty' },
];

const faqs = [
    {
        q: 'كم يستغرق الرد على الرسائل؟',
        a: BRAND_DATA.responseSla,
    },
    {
        q: 'هل يمكن تعديل الطلب بعد الدفع؟',
        a: 'يمكن ذلك قبل تجهيز الطلب للشحن. تواصل معنا مع رقم الطلب وسيتم التحقق فوراً.',
    },
    {
        q: 'كيف أتابع حالة طلبي؟',
        a: 'ستصلك تحديثات الحالة، ويمكنك متابعة الطلب من صفحة الطلبات داخل حسابك.',
    },
];

function isValidEmail(email: string): boolean {
    const v = String(email || '').trim();
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);
}

export default function ContactPage() {
    const [form, setForm] = useState({
        name: '',
        email: '',
        orderNumber: '',
        subject: 'استفسار عام',
        message: '',
    });

    const [touched, setTouched] = useState(false);

    const errors = useMemo(() => {
        const e: Record<string, string> = {};
        if (!form.name.trim()) {
            e.name = 'الاسم مطلوب.';
        }
        if (!isValidEmail(form.email)) {
            e.email = 'البريد الإلكتروني غير صحيح.';
        }
        if (!form.message.trim() || form.message.trim().length < 10) {
            e.message = 'اكتب رسالة لا تقل عن 10 أحرف.';
        }
        return e;
    }, [form]);

    const canSubmit = useMemo(() => Object.keys(errors).length === 0, [errors]);

    function onChange(key: string) {
        return (
            ev: React.ChangeEvent<
                HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement
            >,
        ) => {
            setForm((p) => ({ ...p, [key]: ev.target.value }));
        };
    }

    function onSubmit(ev: React.FormEvent) {
        ev.preventDefault();
        setTouched(true);
        if (!canSubmit) {
            return;
        }

        const subject = `[${BRAND_DATA.nameAr}] ${form.subject}${
            form.orderNumber.trim() ? ` - طلب: ${form.orderNumber.trim()}` : ''
        }`;

        const bodyLines = [
            `الاسم: ${form.name.trim()}`,
            `البريد: ${form.email.trim()}`,
            form.orderNumber.trim()
                ? `رقم الطلب: ${form.orderNumber.trim()}`
                : null,
            '',
            'الرسالة:',
            form.message.trim(),
            '',
            '—',
            `تم الإرسال من صفحة اتصل بنا (${BRAND_DATA.nameAr})`,
        ].filter(Boolean);

        const mailto = `mailto:${BRAND_DATA.email}?subject=${encodeURIComponent(
            subject,
        )}&body=${encodeURIComponent(bodyLines.join('\n'))}`;

        window.location.href = mailto;
    }

    return (
        <StoreLayout>
            <Head title={`اتصل بنا | ${BRAND_DATA.nameAr}`}>
                <meta
                    name="description"
                    content="تواصل مع زين ماركت عبر البريد أو واتساب، أو أرسل رسالة مباشرة. فريق الدعم متاح خلال ساعات العمل."
                />
            </Head>

            <HeroSection
                icon={MessageCircle}
                nameAr={BRAND_DATA.nameAr}
                nameEn={BRAND_DATA.nameEn}
                title="اتصل بنا"
                description={`فريق ${BRAND_DATA.nameAr} جاهز لمساعدتك قبل الشراء وبعده. اختر وسيلة التواصل المناسبة، أو أرسل رسالة مباشرة.`}
                backgroundImage={ASSETS.heroBg}
                actions={
                    <>
                        <Button
                            asChild
                            size="lg"
                            variant="secondary"
                        >
                            <a href={`mailto:${BRAND_DATA.email}`}>
                                راسلنا عبر البريد
                            </a>
                        </Button>
                        <Button
                            asChild
                            size="lg"
                            variant="outline"
                            className="bg-white/15 text-white ring-2 ring-white/30 backdrop-blur-md transition-all hover:scale-105 hover:bg-white/25 hover:ring-white/40"
                        >
                            <a
                                href={`https://wa.me/${BRAND_DATA.whatsapp.replace('+', '')}`}
                                target="_blank"
                                rel="noopener noreferrer"
                            >
                                تواصل واتساب
                            </a>
                        </Button>
                    </>
                }
                badges={
                    <p className="text-sm text-white/90">
                        ساعات العمل: {BRAND_DATA.hours} •{' '}
                        {BRAND_DATA.responseSla}
                    </p>
                }
            />

            {/* CONTACT CARDS */}
            <section className="relative z-10 container mx-auto -mt-8 px-4 sm:px-6 lg:px-8">
                <div className="grid gap-4 rounded-2xl border border-border bg-card p-6 shadow-lg backdrop-blur-sm sm:grid-cols-2 lg:grid-cols-4">
                    {contactCards.map((card) => {
                        const Icon = card.icon;
                        const CardContent = (
                            <div className="flex items-start gap-4">
                                <span className="grid size-11 shrink-0 place-items-center rounded-xl bg-primary text-primary-foreground shadow-md">
                                    <Icon className="size-5" />
                                </span>
                                <div className="min-w-0 flex-1">
                                    <p className="text-sm font-bold text-foreground">
                                        {card.title}
                                    </p>
                                    <p className="mt-1 text-xs leading-5 text-muted-foreground">
                                        {card.desc}
                                    </p>
                                    <p className="mt-3 text-sm font-semibold break-all text-foreground">
                                        {card.value}
                                    </p>
                                </div>
                            </div>
                        );

                        if (card.external) {
                            return (
                                <a
                                    key={card.title}
                                    href={card.href}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="rounded-xl bg-muted/60 p-5 ring-1 ring-border transition-all hover:-translate-y-1 hover:bg-muted/80 hover:shadow-md focus:ring-2 focus:ring-ring focus:outline-none"
                                >
                                    {CardContent}
                                </a>
                            );
                        }

                        return (
                            <a
                                key={card.title}
                                href={card.href}
                                className="rounded-xl bg-muted/60 p-5 ring-1 ring-border transition-all hover:-translate-y-1 hover:bg-muted/80 hover:shadow-md focus:ring-2 focus:ring-ring focus:outline-none"
                            >
                                {CardContent}
                            </a>
                        );
                    })}
                </div>
            </section>

            {/* FORM + INFO */}
            <section className="container mx-auto px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
                <div className="grid gap-10 lg:grid-cols-12 lg:items-start">
                    {/* Form */}
                    <div className="lg:col-span-7">
                        <div className="rounded-2xl border border-border bg-card p-6 shadow-sm">
                            <div className="flex items-start justify-between gap-4">
                                <div>
                                    <h2 className="text-xl font-bold tracking-tight text-foreground sm:text-2xl">
                                        أرسل رسالة
                                    </h2>
                                    <p className="mt-2 text-sm leading-6 text-muted-foreground">
                                        اكتب التفاصيل وسنقوم بالمتابعة. إذا كان
                                        لديك رقم طلب، أضفه لتسريع المعالجة.
                                    </p>
                                </div>
                                <span className="hidden rounded-xl bg-muted/50 px-3 py-2 text-xs font-medium text-muted-foreground ring-1 ring-border sm:inline">
                                    الرد خلال ساعات العمل
                                </span>
                            </div>

                            <form
                                onSubmit={onSubmit}
                                className="mt-6 space-y-4"
                            >
                                <div className="grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <label className="text-xs font-semibold text-foreground">
                                            الاسم
                                        </label>
                                        <input
                                            type="text"
                                            value={form.name}
                                            onChange={onChange('name')}
                                            placeholder="مثال: أحمد علي"
                                            className={`mt-2 w-full rounded-xl border bg-background px-3 py-2.5 text-sm text-foreground shadow-sm transition outline-none ${
                                                touched && errors.name
                                                    ? 'border-destructive focus:ring-2 focus:ring-destructive/20'
                                                    : 'border-border focus:border-primary focus:ring-2 focus:ring-primary/20'
                                            }`}
                                        />
                                        {touched && errors.name && (
                                            <p className="mt-1 text-xs text-destructive">
                                                {errors.name}
                                            </p>
                                        )}
                                    </div>

                                    <div>
                                        <label className="text-xs font-semibold text-foreground">
                                            البريد الإلكتروني
                                        </label>
                                        <input
                                            type="email"
                                            value={form.email}
                                            onChange={onChange('email')}
                                            placeholder="name@example.com"
                                            className={`mt-2 w-full rounded-xl border bg-background px-3 py-2.5 text-sm text-foreground shadow-sm transition outline-none ${
                                                touched && errors.email
                                                    ? 'border-destructive focus:ring-2 focus:ring-destructive/20'
                                                    : 'border-border focus:border-primary focus:ring-2 focus:ring-primary/20'
                                            }`}
                                        />
                                        {touched && errors.email && (
                                            <p className="mt-1 text-xs text-destructive">
                                                {errors.email}
                                            </p>
                                        )}
                                    </div>
                                </div>

                                <div className="grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <label className="text-xs font-semibold text-foreground">
                                            رقم الطلب (اختياري)
                                        </label>
                                        <input
                                            type="text"
                                            value={form.orderNumber}
                                            onChange={onChange('orderNumber')}
                                            placeholder="RBW-2026-01234"
                                            className="mt-2 w-full rounded-xl border border-border bg-background px-3 py-2.5 text-sm text-foreground shadow-sm transition outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"
                                        />
                                    </div>

                                    <div>
                                        <label className="text-xs font-semibold text-foreground">
                                            نوع الاستفسار
                                        </label>
                                        <select
                                            value={form.subject}
                                            onChange={onChange('subject')}
                                            className="mt-2 w-full rounded-xl border border-border bg-background px-3 py-2.5 text-sm text-foreground shadow-sm transition outline-none focus:border-primary focus:ring-2 focus:ring-primary/20"
                                        >
                                            <option value="استفسار عام">
                                                استفسار عام
                                            </option>
                                            <option value="متابعة طلب">
                                                متابعة طلب
                                            </option>
                                            <option value="إرجاع/استبدال">
                                                إرجاع/استبدال
                                            </option>
                                            <option value="ضمان">ضمان</option>
                                            <option value="مشكلة دفع">
                                                مشكلة دفع
                                            </option>
                                            <option value="اقتراحات">
                                                اقتراحات
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label className="text-xs font-semibold text-foreground">
                                        الرسالة
                                    </label>
                                    <textarea
                                        value={form.message}
                                        onChange={onChange('message')}
                                        placeholder="اكتب تفاصيل المشكلة أو الاستفسار بشكل واضح..."
                                        rows={5}
                                        className={`mt-2 w-full rounded-xl border bg-background px-3 py-2.5 text-sm text-foreground shadow-sm transition outline-none ${
                                            touched && errors.message
                                                ? 'border-destructive focus:ring-2 focus:ring-destructive/20'
                                                : 'border-border focus:border-primary focus:ring-2 focus:ring-primary/20'
                                        }`}
                                    />
                                    {touched && errors.message && (
                                        <p className="mt-1 text-xs text-destructive">
                                            {errors.message}
                                        </p>
                                    )}
                                </div>

                                <div className="flex flex-wrap items-center justify-between gap-3 pt-2">
                                    <p className="text-xs text-muted-foreground">
                                        بالضغط على "إرسال" سيتم فتح برنامج
                                        البريد لديك لإرسال الرسالة إلى{' '}
                                        <span className="font-semibold text-foreground">
                                            {BRAND_DATA.email}
                                        </span>
                                        .
                                    </p>

                                    <Button
                                        type="submit"
                                        disabled={!canSubmit}
                                        size="lg"
                                        className="text-base font-semibold"
                                    >
                                        إرسال
                                    </Button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {/* Sidebar */}
                    <div className="lg:col-span-5">
                        <div className="space-y-6">
                            <div className="rounded-2xl border border-border bg-muted/50 p-6">
                                <h3 className="text-base font-bold text-foreground">
                                    معلومات سريعة
                                </h3>
                                <ul className="mt-4 space-y-3 text-sm text-muted-foreground">
                                    <li className="flex items-start gap-2">
                                        <Clock className="mt-0.5 size-4 shrink-0 text-primary" />
                                        <span>
                                            ساعات العمل: {BRAND_DATA.hours}
                                        </span>
                                    </li>
                                    <li className="flex items-start gap-2">
                                        <MapPin className="mt-0.5 size-4 shrink-0 text-primary" />
                                        <span>
                                            المقر: {BRAND_DATA.location}
                                        </span>
                                    </li>
                                    <li className="flex items-start gap-2">
                                        <Shield className="mt-0.5 size-4 shrink-0 text-primary" />
                                        <span>
                                            سياسات واضحة للشحن/الإرجاع/الضمان.
                                        </span>
                                    </li>
                                </ul>

                                <div className="mt-5 flex flex-wrap gap-2">
                                    {policies.map((p) => (
                                        <Link
                                            key={p.title}
                                            href={p.href}
                                            className="rounded-full bg-background px-3 py-1 text-xs font-medium text-foreground ring-1 ring-border transition-all hover:bg-muted hover:ring-primary/30"
                                        >
                                            {p.title}
                                        </Link>
                                    ))}
                                </div>
                            </div>

                            <div className="rounded-2xl border border-border bg-card p-6 shadow-sm">
                                <h3 className="text-base font-bold text-foreground">
                                    أسئلة شائعة
                                </h3>

                                <div className="mt-4 space-y-4">
                                    {faqs.map((f) => (
                                        <InfoBox key={f.q}>
                                            <p className="text-sm font-bold text-foreground">
                                                {f.q}
                                            </p>
                                            <p className="mt-2 text-sm leading-6 text-muted-foreground">
                                                {f.a}
                                            </p>
                                        </InfoBox>
                                    ))}
                                </div>

                                <div className="mt-5">
                                    <Link
                                        href="/help"
                                        className="inline-flex items-center gap-2 text-sm font-semibold text-primary transition-all hover:gap-3"
                                    >
                                        مركز المساعدة
                                        <span aria-hidden="true">←</span>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* MAP */}
            <section
                id="map"
                className="bg-muted/50"
            >
                <div className="container mx-auto px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
                    <div className="grid gap-8 lg:grid-cols-12 lg:items-center">
                        <div className="lg:col-span-5">
                            <h2 className="text-2xl font-bold tracking-tight text-foreground sm:text-3xl">
                                موقعنا
                            </h2>
                            <p className="mt-3 text-base leading-7 text-muted-foreground">
                                مركز العمليات في الرياض. هذا يساعدنا على تجهيز
                                الطلبات بسرعة وتغطية مناطق الشحن بكفاءة.
                            </p>

                            <div className="mt-5 rounded-2xl border border-border bg-card p-5 shadow-sm">
                                <div className="flex items-start gap-3">
                                    <span className="grid size-10 shrink-0 place-items-center rounded-xl bg-primary text-primary-foreground">
                                        <MapPin className="size-5" />
                                    </span>
                                    <div>
                                        <p className="text-sm font-bold text-foreground">
                                            {BRAND_DATA.location}
                                        </p>
                                        <p className="mt-1 text-sm text-muted-foreground">
                                            تواصل قبل زيارتنا (إن وُجد استلام)
                                            للتأكد من توفر الخدمة.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="lg:col-span-7">
                            <div className="overflow-hidden rounded-2xl border border-border bg-muted/50 shadow-sm">
                                <iframe
                                    title="خريطة الرياض"
                                    className="h-[360px] w-full"
                                    loading="lazy"
                                    referrerPolicy="no-referrer-when-downgrade"
                                    src="https://www.openstreetmap.org/export/embed.html?bbox=46.578%2C24.624%2C46.850%2C24.814&layer=mapnik&marker=24.7136%2C46.6753"
                                />
                            </div>
                            <p className="mt-3 text-xs text-muted-foreground">
                                قد تختلف الدقة حسب مزود الخرائط.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <FooterCTA
                title="تحتاج مساعدة الآن؟"
                description="استخدم واتساب للرد السريع، أو راسلنا عبر البريد لأي تفاصيل إضافية."
                brandName={BRAND_DATA.nameAr}
                primaryActions={
                    <Button
                        asChild
                        size="lg"
                        variant="secondary"
                    >
                        <a
                            href={`https://wa.me/${BRAND_DATA.whatsapp.replace('+', '')}`}
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            تواصل واتساب
                        </a>
                    </Button>
                }
                contactBadges={
                    <>
                        <ContactBadge
                            icon={Mail}
                            href={`mailto:${BRAND_DATA.email}`}
                        >
                            {BRAND_DATA.email}
                        </ContactBadge>
                        <ContactBadge
                            icon={MessageCircle}
                            href={`https://wa.me/${BRAND_DATA.whatsapp.replace('+', '')}`}
                            external
                        >
                            {BRAND_DATA.whatsappDisplay}
                        </ContactBadge>
                    </>
                }
            />
        </StoreLayout>
    );
}
