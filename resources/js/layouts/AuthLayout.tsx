import StoreLayout from '@/layouts/StoreLayout';
import AuthLayoutTemplate from '@/layouts/auth/auth-simple-layout';

export default function AuthLayout({
    children,
    title,
    description,
    ...props
}: {
    children: React.ReactNode;
    title: string;
    description: string;
}) {
    return (
        <>
            <div className="lg:hidden">
                <AuthLayoutTemplate
                    title={title}
                    description={description}
                    {...props}
                >
                    {children}
                </AuthLayoutTemplate>
            </div>
            <div className="hidden lg:block">
                <StoreLayout>
                    <div className="container mx-auto px-4 py-12">
                        <AuthLayoutTemplate
                            title={title}
                            description={description}
                            variant="embedded"
                            {...props}
                        >
                            {children}
                        </AuthLayoutTemplate>
                    </div>
                </StoreLayout>
            </div>
        </>
    );
}
