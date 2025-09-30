import { useAppearance } from '@/hooks/use-appearance';
import { useIsMobile } from '@/hooks/use-mobile';
import { Monitor, Moon, Sun } from 'lucide-react';
import { FC } from 'react';
import { Button } from './ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from './ui/dropdown-menu';

type Props = {
  width?: 'fit' | 'full';
};

const ThemeToggler: FC<Props> = () => {
  const { appearance, updateAppearance } = useAppearance();
  const mobile = useIsMobile();

  return (
    <DropdownMenu>
      <DropdownMenuTrigger asChild>
        <Button variant={'outline'} size={mobile ? 'icon' : 'default'}>
          {appearance === 'light' && <Sun />}
          {appearance === 'dark' && <Moon />}
          {appearance === 'system' && <Monitor />}
          {!mobile && (
            <span>
              {appearance === 'light' && 'Terang'}
              {appearance === 'dark' && 'Gelap'}
              {appearance === 'system' && 'Sistem'}
            </span>
          )}
        </Button>
      </DropdownMenuTrigger>
      <DropdownMenuContent align="end">
        <DropdownMenuItem onClick={() => updateAppearance('light')}>
          <Sun />
          <span>Terang</span>
        </DropdownMenuItem>
        <DropdownMenuItem onClick={() => updateAppearance('dark')}>
          <Moon />
          <span>Gelap</span>
        </DropdownMenuItem>
        <DropdownMenuItem onClick={() => updateAppearance('system')}>
          <Monitor />
          <span>Sistem</span>
        </DropdownMenuItem>
      </DropdownMenuContent>
    </DropdownMenu>
  );
};

export default ThemeToggler;
