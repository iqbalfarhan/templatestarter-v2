import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import { groupBy } from '@/lib/utils';
import { Permission } from '@/types/permission';
import { Link } from '@inertiajs/react';
import { ArrowLeft, Edit, Filter, Folder, Plus, Trash2 } from 'lucide-react';
import { FC, useState } from 'react';
import PermissionBulkDeleteDialog from './components/permission-bulk-delete-dialog';
import PermissionBulkEditSheet from './components/permission-bulk-edit-sheet';
import PermissionDeleteDialog from './components/permission-delete-dialog';
import PermissionFilterSheet from './components/permission-filter-sheet';
import PermissionFormSheet from './components/permission-form-sheet';

type Props = {
  permissions: Permission[];
  query: { [key: string]: string };
};

const PermissionList: FC<Props> = ({ permissions, query }) => {
  const [ids, setIds] = useState<number[]>([]);
  const [cari, setCari] = useState('');

  const permissionGroup = groupBy(permissions, 'group');

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
        <PermissionFilterSheet query={query}>
          <Button>
            <Filter />
            Filter data
            {Object.values(query).filter((val) => val && val !== '').length > 0 && (
              <Badge variant="secondary">{Object.values(query).filter((val) => val && val !== '').length}</Badge>
            )}
          </Button>
        </PermissionFilterSheet>
        {ids.length > 0 && (
          <>
            <Button variant={'ghost'} disabled>
              {ids.length} item selected
            </Button>
            <PermissionBulkEditSheet permissionIds={ids}>
              <Button>
                <Edit /> Edit selected
              </Button>
            </PermissionBulkEditSheet>
            <PermissionBulkDeleteDialog permissionIds={ids}>
              <Button variant={'destructive'}>
                <Trash2 /> Delete selected
              </Button>
            </PermissionBulkDeleteDialog>
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
            <TableHead>Group</TableHead>
            <TableHead>Permission Name</TableHead>
            <TableHead>Actions</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          {Object.entries(permissionGroup)
            .filter((permission) => JSON.stringify(permission).toLowerCase().includes(cari.toLowerCase()))
            .map(([group, permissions]) =>
              permissions.map((permission) => (
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
                  <TableCell>{group}</TableCell>
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
              )),
            )}
        </TableBody>
      </Table>
    </AppLayout>
  );
};

export default PermissionList;
