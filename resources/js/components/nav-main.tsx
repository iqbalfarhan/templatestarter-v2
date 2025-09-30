import {
  SidebarGroup,
  SidebarGroupLabel,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  SidebarMenuSub,
  SidebarMenuSubButton,
  SidebarMenuSubItem,
} from '@/components/ui/sidebar';
import { strLimit } from '@/lib/utils';
import { NestedNavItem } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { ChevronRight } from 'lucide-react';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from './ui/collapsible';

type Props = {
  items: NestedNavItem[];
  label?: string;
};

export function NavMain({ items = [], label }: Props) {
  const { url } = usePage();

  const isActive = (href: string) => {
    try {
      const hrefUrl = new URL(href);
      const hrefPath = hrefUrl.pathname;

      // Extract pathname from current URL (remove query params and hash)
      const currentPath = url.split('?')[0].split('#')[0];

      // kalau ada wildcard "*"
      if (hrefPath.endsWith('/*')) {
        const base = hrefPath.replace('/*', '');
        return currentPath === base || currentPath.startsWith(base + '/');
      }

      // fallback: kalau href exact atau url mulai dengan path + "/"
      return currentPath === hrefPath || currentPath.startsWith(hrefPath + '/');
    } catch {
      return false;
    }
  };

  // kalau items kosong, skip
  if (items.length === 0) return null;

  // kalau semua item.available === false, skip
  const hasAvailable = items.some((item) => item.available !== false);
  if (!hasAvailable) return null;

  return (
    <SidebarGroup className="px-2 py-0">
      <SidebarGroupLabel>{label ? label : 'Main Navigation'}</SidebarGroupLabel>
      <SidebarMenu>
        {items.map((item) => {
          if (item.available === false) return null;

          if (item.items) {
            const collapsibleOpen = item.items.some((item) => isActive(item.href));
            return (
              <Collapsible key={item.title} asChild defaultOpen={collapsibleOpen} className="group/collapsible">
                <SidebarMenuItem>
                  <CollapsibleTrigger asChild>
                    <SidebarMenuButton>
                      {item.icon && <item.icon />}
                      {item.title}
                      <ChevronRight className="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
                    </SidebarMenuButton>
                  </CollapsibleTrigger>
                  <CollapsibleContent>
                    <SidebarMenuSub>
                      {item.items.map((item) => (
                        <SidebarMenuSubItem key={item.title}>
                          <SidebarMenuSubButton asChild isActive={isActive(item.href)}>
                            <Link href={item.href} prefetch>
                              <span>{strLimit(item.title, 25)}</span>
                            </Link>
                          </SidebarMenuSubButton>
                        </SidebarMenuSubItem>
                      ))}
                    </SidebarMenuSub>
                  </CollapsibleContent>
                </SidebarMenuItem>
              </Collapsible>
            );
          }
          return (
            <SidebarMenuItem key={item.title}>
              <SidebarMenuButton asChild isActive={isActive(item.href)} tooltip={{ children: item.title }}>
                <Link href={item.href} prefetch>
                  {item.icon && <item.icon />}
                  <span>{strLimit(item.title, 25)}</span>
                </Link>
              </SidebarMenuButton>
            </SidebarMenuItem>
          );
        })}
      </SidebarMenu>
    </SidebarGroup>
  );
}
