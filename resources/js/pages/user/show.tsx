import { Card, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { backAction } from '@/lib/utils';
import { User } from '@/types/user';
import { Edit } from 'lucide-react';
import { FC, useState } from 'react';
import UserFormSheet from './components/user-form-sheet';

type Props = {
  user: User;
};

const ShowUser: FC<Props> = ({ user }) => {
  const [openEditSheet, setOpenEditSheet] = useState(false);

  return (
    <AppLayout
      title="Detail User"
      description="Detail user"
      actions={[
        backAction(),
        {
          title: 'Edit user',
          onClick: () => setOpenEditSheet(true),
          icon: Edit,
        },
      ]}
    >
      <UserFormSheet
        open={openEditSheet}
        onOpenChange={setOpenEditSheet}
        purpose="edit"
        user={user}
        onSuccess={() => setOpenEditSheet(false)}
        withChildren={false}
      />
      <Card>
        <CardHeader>
          <CardTitle>{user.name}</CardTitle>
          <CardDescription>{user.email}</CardDescription>
        </CardHeader>
      </Card>
    </AppLayout>
  );
};

export default ShowUser;
