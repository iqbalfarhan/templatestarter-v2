import { Avatar, AvatarImage } from '@/components/ui/avatar';
import { Card, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { SharedData } from '@/types';
import { router, usePage } from '@inertiajs/react';

const UserInfoWidget = () => {
  const {
    auth: { user },
  } = usePage<SharedData>().props;

  return (
    <Card onClick={() => router.visit(route('profile.edit'))} className="cursor-pointer">
      <div className="flex justify-between">
        <CardHeader>
          <Avatar className="size-10">
            <AvatarImage src={user.avatar} alt={user.name} />
          </Avatar>
        </CardHeader>
        <CardHeader className="flex-1 pl-0">
          <CardTitle>{user.name}</CardTitle>
          <CardDescription>{user.email}</CardDescription>
        </CardHeader>
      </div>
    </Card>
  );
};

export default UserInfoWidget;
