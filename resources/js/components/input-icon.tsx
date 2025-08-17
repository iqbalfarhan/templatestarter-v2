import { Input } from '@/components/ui/input';
import { LucideIcon, Search } from 'lucide-react';
import { ComponentProps, FC } from 'react';

type Props = ComponentProps<typeof Input> & {
  icon?: LucideIcon;
};

const InputIcon: FC<Props> = ({ icon: Icon = Search, className, ...other }) => {
  return (
    <div className="relative w-full max-w-sm">
      <Icon className="absolute top-1/2 left-2.5 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
      <Input type="text" className={`pl-8 ${className ?? ''}`} {...other} />
    </div>
  );
};

export default InputIcon;
