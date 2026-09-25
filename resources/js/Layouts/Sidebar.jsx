import { Link, usePage } from '@inertiajs/react';

const NAV_ITEMS = [
    { label: 'Dashboard', href: route('dashboard'), match: 'dashboard', roles: ['super_admin', 'admin', 'assignee'] },
    { label: 'Inventory', href: route('inventory.index'), match: 'inventory.*', roles: ['super_admin', 'admin', 'assignee'] },
    { label: 'Issue Products', href: route('issue.index'), match: 'issue.*', roles: ['super_admin', 'admin'] },
    { label: 'Return Products', href: route('return.index'), match: 'return.*', roles: ['super_admin', 'admin'] },
    { label: 'Maintenance', href: route('maintenance.index'), match: 'maintenance.*', roles: ['super_admin', 'admin'] },
    { label: 'Reports', href: route('reports.index'), match: 'reports.*', roles: ['super_admin', 'admin'] },
];

const SETTINGS_ITEMS = [
    { label: 'User Management', href: route('settings.users.index'), match: 'settings.users.*', roles: ['super_admin', 'admin'] },
    { label: 'Categories', href: route('settings.categories.index'), match: 'settings.categories.*', roles: ['super_admin', 'admin'] },
    { label: 'Locations', href: route('settings.locations.index'), match: 'settings.locations.*', roles: ['super_admin', 'admin'] },
    { label: 'Admin Management', href: route('settings.admins.index'), match: 'settings.admins.*', roles: ['super_admin'] },
    { label: 'System Settings', href: route('settings.system.edit'), match: 'settings.system.*', roles: ['super_admin'] },
    { label: 'Audit Logs', href: route('settings.audit.index'), match: 'settings.audit.*', roles: ['super_admin'] },
];

export default function Sidebar() {
    const { auth } = usePage().props;
    const role = auth.user?.role;

    return (
        <aside className="w-64 shrink-0 bg-slate-900 text-slate-200 min-h-screen flex flex-col">
            <div className="px-5 py-5 border-b border-slate-800">
                <div className="text-lg font-semibold text-white">IMDR</div>
                <div className="text-xs text-slate-400">Inventory Management</div>
            </div>

            <nav className="flex-1 px-3 py-4 space-y-1">
                {NAV_ITEMS.filter((item) => item.roles.includes(role)).map((item) => (
                    <SidebarLink key={item.label} {...item} />
                ))}

                {/* Settings: Super Admin sees everything; Admin sees only
                    User Management, Categories, and Locations. */}
                {(role === 'super_admin' || role === 'admin') && (
                    <div className="pt-4 mt-4 border-t border-slate-800">
                        <div className="px-3 pb-2 text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Settings
                        </div>
                        {SETTINGS_ITEMS.filter((item) => item.roles.includes(role)).map((item) => (
                            <SidebarLink key={item.label} {...item} />
                        ))}
                    </div>
                )}
            </nav>

            <div className="px-5 py-4 border-t border-slate-800 text-xs text-slate-500">
                Signed in as
                <div className="text-slate-200 font-medium truncate">{auth.user?.full_name}</div>
                <div className="capitalize mb-3">{role?.replace('_', ' ')}</div>

                <Link
                    href={route('profile.edit')}
                    className="block rounded-lg px-3 py-2 text-sm text-slate-300 hover:bg-slate-800 hover:text-white transition"
                >
                    My Profile
                </Link>
                <Link
                    href={route('logout')}
                    method="post"
                    as="button"
                    className="w-full text-left block rounded-lg px-3 py-2 text-sm text-slate-300 hover:bg-slate-800 hover:text-white transition"
                >
                    Log out
                </Link>
            </div>
        </aside>
    );
}

function SidebarLink({ label, href, match }) {
    const { url } = usePage();
    const isActive = route().current(match);

    return (
        <Link
            href={href}
            className={`block rounded-lg px-3 py-2 text-sm transition ${
                isActive
                    ? 'bg-brand-600 text-white font-medium'
                    : 'text-slate-300 hover:bg-slate-800 hover:text-white'
            }`}
        >
            {label}
        </Link>
    );
}