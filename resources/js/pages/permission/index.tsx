import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import { Permission } from '@/types/permission';
import { Link } from '@inertiajs/react';
import { ArrowLeft, Edit, Filter, Folder, Plus, Trash2, X } from 'lucide-react';
import { FC, useState } from 'react';
import PermissionDeleteDialog from './components/permission-delete-dialog';
import PermissionFilterSheet from './components/permission-filter-sheet';
import PermissionFormSheet from './components/permission-form-sheet';

type Props = {
  permissions: Permission[];
};

const PermissionList: FC<Props> = ({ permissions }) => {
  const [ids, setIds] = useState<number[]>([]);
  const [cari, setCari] = useState('');

  return (
    <AppLayout
      title="Permissions"
      description="Manage your permissions"
      actions={
        <>
          <Button asChild variant={'secondary'}>
            <Link href={route('role.index')}>
              <ArrowLeft />
              Kembali ke list role
            </Link>
          </Button>
          <PermissionFormSheet purpose="create">
            <Button>
              <Plus />
              Create new permission
            </Button>
          </PermissionFormSheet>
        </>
      }
    >
      <div className="flex gap-2">
        <Input placeholder="Search permissions..." value={cari} onChange={(e) => setCari(e.target.value)} />
        {cari && (
          <Button variant={'ghost'} onClick={() => setCari('')}>
            <X />
            Clear search
          </Button>
        )}
        <PermissionFilterSheet>
          <Button>
            <Filter />
            Filter data
          </Button>
        </PermissionFilterSheet>
        {ids.length > 0 && (
          <>
            <Button variant={'ghost'} disabled>
              {ids.length} item selected
            </Button>
            <Button>
              <Edit /> Edit selected
            </Button>
            <Button variant={'destructive'}>
              <Trash2 /> Delete selected
            </Button>
          </>
        )}
      </div>
      <Table>
        <TableHeader>
          <TableRow>
            <TableHead>
              <Button variant={'ghost'} size={'icon'} asChild>
                <Label>
                  <Checkbox
                    checked={ids.length === permissions.length}
                    onCheckedChange={(checked) => {
                      if (checked) {
                        setIds(permissions.map((permission) => permission.id));
                      } else {
                        setIds([]);
                      }
                    }}
                  />
                </Label>
              </Button>
            </TableHead>
            <TableHead>Group name</TableHead>
            <TableHead>Name</TableHead>
            <TableHead>Actions</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          {permissions
            .filter((permission) => JSON.stringify(permission).toLowerCase().includes(cari.toLowerCase()))
            .map((permission) => (
              <TableRow key={permission.id}>
                <TableCell>
                  <Button variant={'ghost'} size={'icon'} asChild>
                    <Label>
                      <Checkbox
                        checked={ids.includes(permission.id)}
                        onCheckedChange={(checked) => {
                          if (checked) {
                            setIds([...ids, permission.id]);
                          } else {
                            setIds(ids.filter((id) => id !== permission.id));
                          }
                        }}
                      />
                    </Label>
                  </Button>
                </TableCell>
                <TableCell>
                  <div onClick={() => setCari(permission.group)}>{permission.group}</div>
                </TableCell>
                <TableCell>{permission.name}</TableCell>
                <TableCell>
                  <Button variant={'ghost'} size={'icon'}>
                    <Link href={route('permission.show', permission.id)}>
                      <Folder />
                    </Link>
                  </Button>
                  <PermissionFormSheet purpose="edit" permission={permission}>
                    <Button variant={'ghost'} size={'icon'}>
                      <Edit />
                    </Button>
                  </PermissionFormSheet>
                  <PermissionDeleteDialog permission={permission}>
                    <Button variant={'ghost'} size={'icon'}>
                      <Trash2 />
                    </Button>
                  </PermissionDeleteDialog>
                </TableCell>
              </TableRow>
            ))}
        </TableBody>
      </Table>
    </AppLayout>
  );
};

export default PermissionList;
