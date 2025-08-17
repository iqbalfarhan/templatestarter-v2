import AppLayout from '@/layouts/app-layout';
import { SharedData, type BreadcrumbItem } from '@/types';
import { usePage } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: '/dashboard',
  },
];

export default function Dashboard() {
  const {
    auth: { roles },
  } = usePage<SharedData>().props;
  return (
    <AppLayout title="Dashboard" description={`Selamat datang, kamu masuk sebagai ${roles.join(', ')}`} breadcrumbs={breadcrumbs}>
      Lorem ipsum dolor sit, amet consectetur adipisicing elit. Sequi praesentium, possimus ad quasi hic rerum, deleniti cumque optio odit excepturi
      necessitatibus reiciendis consequuntur omnis laborum facere consectetur quaerat commodi voluptates.
    </AppLayout>
  );
}
