import { NavFooter } from '@/components/nav-footer';
import { NavUser } from '@/components/nav-user';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { DropdownItem, type NavItem } from '@/types';
import { Link } from '@inertiajs/react';
import { BookOpen, Folder, LayoutGrid, SquareTerminal } from 'lucide-react';
import AppLogo from './app-logo';
import { NavDropdown } from './nav-dropdown';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Data',
        href: '/data',
        icon: LayoutGrid,
    },
];

const dropdownNav: DropdownItem[] = [
    {
        title: 'Home',
        url: '/dashboard',
        icon: LayoutGrid,
        isActive: true,
    },
    {
        title: 'Data',
        url: '#',
        icon: SquareTerminal,
        isActive: true,
        items: [
            {
                title: 'Guru',
                url: '/data/guru',
            },
            {
                title: 'Jurusan',
                url: '/data/jurusan',
            },
            {
                title: 'Kelas',
                url: '/data/kelas',
            },
            {
                title: 'Mapel',
                url: '/data/mapel',
            },
            {
                title: 'Jam Pelajaran',
                url: '/data/jam',
            },
        ],
    },
    {
        title: 'Manage',
        url: '#',
        icon: SquareTerminal,
        isActive: true,
        items: [
            {
                title: 'User',
                url: '/manage/users',
            },
            {
                title: 'Group',
                url: '/manage/groups',
            },
        ],
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/react-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#react',
        icon: BookOpen,
    },
];

export function AppSidebar() {
    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href="/dashboard" prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                {/* <NavMain items={mainNavItems} /> */}
                <NavDropdown items={dropdownNav} mainTitle="Admin" />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
