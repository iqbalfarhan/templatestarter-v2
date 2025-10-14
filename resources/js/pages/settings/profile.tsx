import { type BreadcrumbItem } from '@/types';
import { Transition } from '@headlessui/react';
import { Form, Link } from '@inertiajs/react';

import DeleteUser from '@/components/delete-user';
import FormControl from '@/components/form-control';
import HeadingSmall from '@/components/heading-small';
import InputError from '@/components/input-error';
import { Avatar, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import SettingsLayout from '@/layouts/settings/layout';
import { User } from '@/types/user';
import UserUploadMediaSheet from '../user/components/user-upload-media-sheet';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Profile settings',
    href: '/settings/profile',
  },
];

type Props = {
  mustVerifyEmail: boolean;
  status?: string;
  user: User;
};

export default function Profile({ mustVerifyEmail, status, user }: Props) {
  return (
    <SettingsLayout breadcrumbs={breadcrumbs}>
      <div className="space-y-6">
        <HeadingSmall title="Profile information" description="Update your name and email address" />

        <FormControl label="Avatar">
          <UserUploadMediaSheet user={user} collection_name={'avatar'}>
            <Avatar className="size-24">
              <AvatarImage src={user.avatar} />
            </Avatar>
          </UserUploadMediaSheet>
        </FormControl>

        <Form
          method="patch"
          action={route('profile.update')}
          options={{
            preserveScroll: true,
          }}
          className="space-y-6"
        >
          {({ processing, recentlySuccessful, errors }) => (
            <>
              <div className="grid gap-2">
                <Label htmlFor="name">Name</Label>

                <Input
                  id="name"
                  className="mt-1 block w-full"
                  defaultValue={user.name}
                  name="name"
                  required
                  autoComplete="name"
                  placeholder="Full name"
                />

                <InputError className="mt-2" message={errors.name} />
              </div>

              <div className="grid gap-2">
                <Label htmlFor="email">Email address</Label>

                <Input
                  id="email"
                  type="email"
                  className="mt-1 block w-full"
                  defaultValue={user.email}
                  name="email"
                  required
                  autoComplete="username"
                  placeholder="Email address"
                />

                <InputError className="mt-2" message={errors.email} />
              </div>

              {mustVerifyEmail && user.email_verified_at === null && (
                <div>
                  <p className="-mt-4 text-sm text-muted-foreground">
                    Your email address is unverified.{' '}
                    <Link
                      href={route('verification.send')}
                      method="post"
                      as="button"
                      className="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                    >
                      Click here to resend the verification email.
                    </Link>
                  </p>

                  {status === 'verification-link-sent' && (
                    <div className="mt-2 text-sm font-medium text-green-600">A new verification link has been sent to your email address.</div>
                  )}
                </div>
              )}

              <div className="flex items-center gap-4">
                <Button disabled={processing}>Save</Button>

                <Transition
                  show={recentlySuccessful}
                  enter="transition ease-in-out"
                  enterFrom="opacity-0"
                  leave="transition ease-in-out"
                  leaveTo="opacity-0"
                >
                  <p className="text-sm text-neutral-600">Saved</p>
                </Transition>
              </div>
            </>
          )}
        </Form>
      </div>

      <DeleteUser />
    </SettingsLayout>
  );
}
