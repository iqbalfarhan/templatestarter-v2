import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardTitle } from '@/components/ui/card';
import { User } from '@/types/user';
import { Link } from '@inertiajs/react';
import { Edit, Folder, Trash2 } from 'lucide-react';
import { FC } from 'react';
import UserDeleteDialog from './user-delete-dialog';
import UserFormSheet from './user-form-sheet';

type Props = {
  user: User;
  onClick?: () => void;
};

const UserItemCard: FC<Props> = ({ user, onClick }) => {
  return (
    <Card className="flex flex-col justify-between" onClick={onClick}>
      <CardContent className="flex flex-col items-center justify-center space-y-6">
        <Avatar className="size-20">
          <AvatarFallback>{user.name.charAt(0).toUpperCase()}</AvatarFallback>
          <AvatarImage src={user.avatar} alt={user.name} />
        </Avatar>

        <div className="flex flex-col items-center">
          <CardTitle className="line-clamp-1">{user.name}</CardTitle>
          <CardDescription className="line-clamp-1">{user.email}</CardDescription>
        </div>

        <div className="flex justify-center">
          <Button variant="ghost" size="icon" asChild>
            <Link href={route('user.show', user.id)}>
              <Folder />
            </Link>
          </Button>
          <UserFormSheet purpose="edit" user={user}>
            <Button variant="ghost" size="icon">
              <Edit />
            </Button>
          </UserFormSheet>
          <UserDeleteDialog user={user}>
            <Button variant="ghost" size="icon">
              <Trash2 />
            </Button>
          </UserDeleteDialog>
        </div>
      </CardContent>
    </Card>
  );
};

export default UserItemCard;
