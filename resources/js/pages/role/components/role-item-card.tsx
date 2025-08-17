import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Edit, Trash2, Folder } from 'lucide-react';
import { FC } from 'react';
import { Role } from '@/types/role';
import { Link } from '@inertiajs/react';
import RoleFormSheet from './role-form-sheet';
import RoleDeleteDialog from './role-delete-dialog';

type Props = {
  role: Role;
};

const RoleItemCard: FC<Props> = ({ role }) => {
  return (
    <Card className="flex flex-col justify-between">
      <CardHeader>
        <CardTitle>{ role.name }</CardTitle>
      </CardHeader>
      <CardContent>
        <p className="text-sm text-muted-foreground">
          ID: { role.id }
        </p>
      </CardContent>
      <CardFooter className="flex gap-2">
        <Button variant="ghost" size="icon" asChild>
          <Link href={route('role.show', role.id)}>
            <Folder />
          </Link>
        </Button>
        <RoleFormSheet purpose="edit" role={ role }>
          <Button variant="ghost" size="icon">
            <Edit />
          </Button>
        </RoleFormSheet>
        <RoleDeleteDialog role={ role }>
          <Button variant="ghost" size="icon">
            <Trash2 />
          </Button>
        </RoleDeleteDialog>
      </CardFooter>
    </Card>
  );
};

export default RoleItemCard;
