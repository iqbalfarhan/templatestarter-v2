import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Toaster } from '@/components/ui/sonner';
import { useIsMobile } from '@/hooks/use-mobile';
import AppLayoutTemplate from '@/layouts/app/app-sidebar-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { EllipsisVertical } from 'lucide-react';
import { PropsWithChildren, type ReactNode } from 'react';

type AppLayoutProps = PropsWithChildren & {
  title?: string;
  description?: string;
  breadcrumbs?: BreadcrumbItem[];
  actions?: ReactNode;
};

export default ({
  children,
  breadcrumbs = [
    {
      title: 'Dashboard',
      href: route('dashboard'),
    },
  ],
  title = 'Page Heading',
  description = 'Page description',
  actions,
}: AppLayoutProps) => {
  const mobile = useIsMobile();

  return (
    <AppLayoutTemplate breadcrumbs={breadcrumbs}>
      <Head title={title} />
      <div className="mx-auto flex h-full w-full max-w-7xl flex-1 flex-col gap-4 space-y-4 overflow-x-auto rounded-xl p-6">
        <div className="flex items-start justify-between gap-6">
          <Heading title={title} description={description} />
          {actions && (
            <>
              {mobile ? (
                <>
                  <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                      <Button size={'icon'} variant={'outline'}>
                        <EllipsisVertical />
                      </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent side="bottom" align="end">
                      <>{actions}</>
                    </DropdownMenuContent>
                  </DropdownMenu>
                </>
              ) : (
                <div className="ml-auto flex items-center gap-2">{actions}</div>
              )}
            </>
          )}
        </div>
        {children}
      </div>
      <Toaster position="top-center" />
    </AppLayoutTemplate>
  );
};
