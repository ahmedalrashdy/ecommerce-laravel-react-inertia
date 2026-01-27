import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { useNewsletterSubscription } from '@/hooks/use-newsletter-subscription';
import { Link, usePage } from '@inertiajs/react';
import {
    Facebook,
    Instagram,
    Mail,
    MapPin,
    Phone,
    Send,
    Twitter,
    Youtube,
} from 'lucide-react';
import * as React from 'react';
import { about } from '@/routes/store';

export const Footer: React.FC = () => {
    const { settings } = usePage<{
        settings?: {
            general?: {
                store_name?: string;
                store_description?: string;
            };
            contact?: {
                phone?: string;
                email?: string;
                address?: string;
            };
            social?: {
                facebook_url?: string | null;
                twitter_url?: string | null;
                instagram_url?: string | null;
                youtube_url?: string | null;
            };
        };
    }>().props;

    const storeName = settings?.general?.store_name || 'متجري';
    const storeDescription =
        settings?.general?.store_description ||
        'وجهتك الأولى للتسوق الإلكتروني. نوفر لك أفضل المنتجات بأسعار تنافسية وخدمة عملاء متميزة.';
    const phone = settings?.contact?.phone || '+966 50 000 0000';
    const email = settings?.contact?.email || 'info@mystore.com';
    const address =
        settings?.contact?.address || 'الرياض، المملكة العربية السعودية';
    const facebookUrl = settings?.social?.facebook_url;
    const twitterUrl = settings?.social?.twitter_url;
    const instagramUrl = settings?.social?.instagram_url;
    const youtubeUrl = settings?.social?.youtube_url;

    const {
        email: newsletterEmail,
        setEmail: setNewsletterEmail,
        handleSubmit,
        isLoading,
        isSuccess,
        successMessage,
        errorMessage,
    } = useNewsletterSubscription({
        resetAfterSuccess: true,
        resetDelay: 3000,
    });

    return (
        <footer className="mb-16 lg:mb-0">
            <div className="hidden border-t border-border bg-muted/30 text-foreground/80 lg:block dark:bg-muted/10">
                {/* Main Footer Content */}
                <div className="container mx-auto px-4 py-12 md:py-16">
                    <div className="grid grid-cols-1 gap-8 md:grid-cols-2 md:gap-12 lg:grid-cols-4">
                        {/* About Section */}
                        <div>
                            <h3 className="mb-4 text-2xl font-bold text-primary">
                                {storeName}
                            </h3>
                            <p className="mb-6 text-sm leading-relaxed">
                                {storeDescription}
                            </p>
                            <div className="flex gap-3">
                                {facebookUrl && (
                                    <Link
                                        href={facebookUrl}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="flex h-10 w-10 items-center justify-center rounded-full bg-muted transition-smooth hover:bg-primary hover:text-primary-foreground"
                                    >
                                        <Facebook className="h-5 w-5" />
                                    </Link>
                                )}
                                {twitterUrl && (
                                    <Link
                                        href={twitterUrl}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="flex h-10 w-10 items-center justify-center rounded-full bg-muted transition-smooth hover:bg-primary hover:text-primary-foreground"
                                    >
                                        <Twitter className="h-5 w-5" />
                                    </Link>
                                )}
                                {instagramUrl && (
                                    <Link
                                        href={instagramUrl}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="flex h-10 w-10 items-center justify-center rounded-full bg-muted transition-smooth hover:bg-primary hover:text-primary-foreground"
                                    >
                                        <Instagram className="h-5 w-5" />
                                    </Link>
                                )}
                                {youtubeUrl && (
                                    <Link
                                        href={youtubeUrl}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="flex h-10 w-10 items-center justify-center rounded-full bg-muted transition-smooth hover:bg-primary hover:text-primary-foreground"
                                    >
                                        <Youtube className="h-5 w-5" />
                                    </Link>
                                )}
                            </div>
                        </div>

                        {/* Quick Links */}
                        <div>
                            <h4 className="mb-4 text-lg font-semibold text-foreground">
                                روابط سريعة
                            </h4>
                            <ul className="space-y-3">
                                <li>
                                    <Link
                                        href={about.url()}
                                        className="transition-smooth hover:text-primary"
                                    >
                                        من نحن
                                    </Link>
                                </li>
                                <li>
                                    <Link
                                        href="/contact"
                                        className="transition-smooth hover:text-primary"
                                    >
                                        اتصل بنا
                                    </Link>
                                </li>
                                <li>
                                    <Link
                                        href="/help"
                                        className="transition-smooth hover:text-primary"
                                    >
                                        الأسئلة الشائعة والمساعدة
                                    </Link>
                                </li>
                            </ul>
                        </div>

                        {/* Customer Service */}
                        <div>
                            <h4 className="mb-4 text-lg font-semibold text-foreground">
                                خدمة العملاء
                            </h4>
                            <ul className="space-y-3">
                                <li>
                                    <Link
                                        href="account/orders"
                                        className="transition-smooth hover:text-primary"
                                    >
                                        تتبع طلبك
                                    </Link>
                                </li>
                                <li>
                                    <Link
                                        href="/returns"
                                        className="transition-smooth hover:text-primary"
                                    >
                                        الإرجاع والاستبدال
                                    </Link>
                                </li>
                                <li>
                                    <Link
                                        href="/privacy"
                                        className="transition-smooth hover:text-primary"
                                    >
                                        سياسة الخصوصية
                                    </Link>
                                </li>
                                <li>
                                    <Link
                                        href="/terms"
                                        className="transition-smooth hover:text-primary"
                                    >
                                        الشروط والأحكام
                                    </Link>
                                </li>
                            </ul>
                        </div>

                        {/* Contact Info */}
                        <div>
                            <h4 className="mb-4 text-lg font-semibold text-foreground">
                                تواصل معنا
                            </h4>
                            <ul className="space-y-4">
                                <li className="flex items-start gap-3">
                                    <MapPin className="mt-0.5 h-5 w-5 shrink-0 text-primary" />
                                    <span className="text-sm">{address}</span>
                                </li>
                                <li className="flex items-center gap-3">
                                    <Phone className="h-5 w-5 shrink-0 text-primary" />
                                    <a
                                        dir="ltr"
                                        href={`tel:${phone.replace(/\s/g, '')}`}
                                        className="text-sm transition-smooth hover:text-primary"
                                    >
                                        {phone}
                                    </a>
                                </li>
                                <li className="flex items-center gap-3">
                                    <Mail className="h-5 w-5 shrink-0 text-primary" />
                                    <a
                                        href={`mailto:${email}`}
                                        className="text-sm transition-smooth hover:text-primary"
                                    >
                                        {email}
                                    </a>
                                </li>
                            </ul>

                            {/* Quick Newsletter */}
                            <div className="mt-6">
                                <p className="mb-3 text-sm text-foreground">
                                    {isSuccess
                                        ? successMessage ||
                                          'شكراً لك! تم الاشتراك بنجاح'
                                        : 'اشترك في نشرتنا البريدية'}
                                </p>
                                {!isSuccess ? (
                                    <form
                                        onSubmit={handleSubmit}
                                        className="flex gap-2"
                                    >
                                        <div className="flex-1">
                                            <Input
                                                type="email"
                                                placeholder="بريدك الإلكتروني"
                                                value={newsletterEmail}
                                                onChange={(e) =>
                                                    setNewsletterEmail(
                                                        e.target.value,
                                                    )
                                                }
                                                disabled={isLoading}
                                                required
                                                className="h-10 border-border bg-muted text-foreground placeholder:text-muted-foreground"
                                            />
                                            {errorMessage && (
                                                <p className="mt-1 text-xs text-red-600">
                                                    {errorMessage}
                                                </p>
                                            )}
                                        </div>
                                        <Button
                                            type="submit"
                                            size="icon"
                                            disabled={isLoading}
                                            className="h-10 w-10 shrink-0 transition-smooth hover:scale-105"
                                        >
                                            {isLoading ? (
                                                <span className="text-xs">
                                                    ...
                                                </span>
                                            ) : (
                                                <Send className="h-4 w-4" />
                                            )}
                                        </Button>
                                    </form>
                                ) : null}
                            </div>
                        </div>
                    </div>
                </div>

                {/* Bottom Bar */}
                <div className="border-t border-border">
                    <div className="container mx-auto px-4 py-6">
                        <div className="flex flex-col items-center justify-between gap-4 md:flex-row">
                            <p className="text-sm text-muted-foreground">
                                © {new Date().getFullYear()} {storeName}. جميع
                                الحقوق محفوظة.
                            </p>
                            <div className="flex items-center gap-6">
                                <Link
                                    href="/terms"
                                    className="text-sm text-muted-foreground transition-smooth hover:text-primary"
                                >
                                    الشروط والأحكام
                                </Link>
                                <Link
                                    href="/privacy"
                                    className="text-sm text-muted-foreground transition-smooth hover:text-primary"
                                >
                                    سياسة الخصوصية
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    );
};
