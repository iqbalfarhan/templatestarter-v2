import HeadingSmall from '@/components/heading-small';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import { capitalizeWords, em, groupBy } from '@/lib/utils';
import { Permission } from '@/types/permission';
import { Role } from '@/types/role';
import { Link, useForm } from '@inertiajs/react';
import { ArrowLeft, Check, Edit, Plus } from 'lucide-react';
import { FC } from 'react';
import { toast } from 'sonner';
import PermissionFormSheet from '../permission/components/permission-form-sheet';
import RoleFormSheet from './components/role-form-sheet';

type Props = {
  role: Role;
  permissions: Permission[];
};

const ShowRole: FC<Props> = ({ role, permissions }) => {
  const groupPermissions = groupBy(permissions, 'group');

  const { data, setData, put } = useForm({
    permissions: role.permissions.map((permission) => permission.name),
  });

  const handleSubmit = () => {
    put(route('role.update', role.id), {
      preserveScroll: true,
      onSuccess: () => {
        toast.success('Role updated successfully');
      },
      onError: (e) => toast.error(em(e)),
    });
  };

  return (
    <AppLayout
      title="Detail Role"
      description="Detail role"
      actions={
        <>
          <Link href={route('role.index')}>
            <Button variant={'secondary'}>
              <ArrowLeft />
              Back to route list
            </Button>
          </Link>
          <RoleFormSheet purpose="edit" role={role}>
            <Button>
              <Edit /> Edit nama role
            </Button>
          </RoleFormSheet>
          <Button onClick={handleSubmit}>
            <Check /> Simpan
          </Button>
        </>
      }
    >
      <Card>
        <CardHeader>
          <CardTitle>{capitalizeWords(role.name)}</CardTitle>
          <CardDescription>
            {role.permissions.length ? role.permissions?.map((permission) => permission.name).join(', ') : 'Belum ada permission'}
          </CardDescription>
        </CardHeader>
      </Card>

      <HeadingSmall
        title="Permission"
        description="Pilih permission yang ingin digunakan unruk role ini"
        actions={
          <>
            <PermissionFormSheet purpose="create">
              <Button variant={'secondary'}>
                <Plus /> Tambah permission
              </Button>
            </PermissionFormSheet>
          </>
        }
      />

      <div className="grid grid-cols-4 gap-6">
        {Object.entries(groupPermissions).map(([group, permissions]) => (
          <Card key={group}>
            <CardHeader>
              <CardTitle>{group.charAt(0).toUpperCase() + group.slice(1)}</CardTitle>
              <CardDescription>{permissions.length} permissions</CardDescription>
            </CardHeader>
            <CardContent>
              <div className="grid">
                {permissions.map((permission) => (
                  <Label key={permission.id} className="flex h-8 items-center gap-2">
                    <Checkbox
                      defaultChecked={data.permissions.includes(permission.name)}
                      onCheckedChange={(c) =>
                        setData('permissions', c ? [...data.permissions, permission.name] : data.permissions.filter((p) => p !== permission.name))
                      }
                    />
                    {permission.name}
                  </Label>
                ))}
              </div>
            </CardContent>
          </Card>
        ))}
      </div>
    </AppLayout>
  );
};

export default ShowRole;
