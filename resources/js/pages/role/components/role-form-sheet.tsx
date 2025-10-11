import FormControl from '@/components/form-control';
import SubmitButton from '@/components/submit-button';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Sheet, SheetClose, SheetContent, SheetDescription, SheetFooter, SheetHeader, SheetTitle, SheetTrigger } from '@/components/ui/sheet';
import { capitalizeWords, em } from '@/lib/utils';
import { FormPurpose } from '@/types';
import { Role } from '@/types/role';
import { useForm } from '@inertiajs/react';
import { Copy, Edit, LucideIcon, PlusSquare, X } from 'lucide-react';
import { FC, PropsWithChildren, useState } from 'react';
import { toast } from 'sonner';

type Props = PropsWithChildren & {
  role?: Role;
  icon?: LucideIcon;
  buttonLabel?: string;
  purpose: FormPurpose;
  variant?: 'default' | 'icon';
};

const RoleFormSheet: FC<Props> = ({ children, role, purpose, variant = 'default', icon: Icon, buttonLabel }) => {
  const [open, setOpen] = useState(false);

  const { data, setData, put, post, reset, processing } = useForm({
    name: role?.name ?? '',
  });

  const handleSubmit = () => {
    if (purpose === 'create' || purpose === 'duplicate') {
      post(route('role.store'), {
        preserveScroll: true,
        onSuccess: () => {
          toast.success('Role created successfully');
          reset();
          setOpen(false);
        },
        onError: (e) => toast.error(em(e)),
      });
    } else {
      put(route('role.update', role?.id), {
        preserveScroll: true,
        onSuccess: () => {
          toast.success('Role updated successfully');
        },
        onError: (e) => toast.error(em(e)),
      });
    }
  };

  return (
    <Sheet open={open} onOpenChange={setOpen}>
      {children ? (
        <SheetTrigger asChild>{children}</SheetTrigger>
      ) : (
        <SheetTrigger asChild>
          <Button variant={variant == 'default' ? 'default' : 'ghost'} size={variant == 'default' ? 'default' : 'icon'}>
            {Icon ? <Icon /> : purpose == 'create' ? <PlusSquare /> : purpose == 'edit' ? <Edit /> : <Copy />}
            {variant == 'default' && buttonLabel}
          </Button>
        </SheetTrigger>
      )}
      <SheetContent>
        <SheetHeader>
          <SheetTitle>{capitalizeWords(purpose)} data role</SheetTitle>
          <SheetDescription>Form untuk {purpose} data role</SheetDescription>
        </SheetHeader>
        <ScrollArea className="flex-1 overflow-y-auto">
          <form
            className="space-y-6 px-4"
            onSubmit={(e) => {
              e.preventDefault();
              handleSubmit();
            }}
          >
            <FormControl label="Nama role">
              <Input type="text" placeholder="Name" value={data.name} onChange={(e) => setData('name', e.target.value)} />
            </FormControl>
          </form>
        </ScrollArea>
        <SheetFooter>
          <SubmitButton onClick={handleSubmit} label={`${capitalizeWords(purpose)} role`} loading={processing} disabled={processing} />
          <SheetClose asChild>
            <Button variant={'outline'}>
              <X /> Batalin
            </Button>
          </SheetClose>
        </SheetFooter>
      </SheetContent>
    </Sheet>
  );
};

export default RoleFormSheet;
