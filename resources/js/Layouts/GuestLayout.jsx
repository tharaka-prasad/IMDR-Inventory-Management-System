import { usePage } from '@inertiajs/react';

const FEATURES = [
    { title: 'Asset tracking', text: 'Fixed assets, IT assets and consumables in one register.' },
    { title: 'QR code lookup', text: 'Every asset gets a unique code and QR label automatically.' },
    { title: 'Full audit trail', text: 'Every issue, return and transfer is recorded.' },
];

// Simple decorative QR-like pattern (not a real QR code).
const QR_PATTERN = [
    1, 1, 1, 0, 1, 1, 1,
    1, 0, 1, 1, 1, 0, 1,
    1, 1, 1, 0, 1, 1, 1,
    0, 1, 0, 1, 0, 1, 0,
    1, 1, 1, 0, 1, 0, 1,
    1, 0, 1, 1, 0, 1, 1,
    1, 1, 1, 0, 1, 1, 1,
];

function Brand({ branding, light = false }) {
    return (
        <div className="flex items-center gap-3">
            <div className="relative">
                {branding?.logo_url ? (
                    <img
                        src={branding.logo_url}
                        alt={branding.name}
                        className="relative h-12 w-12 rounded-xl bg-white/10 p-1 object-contain"
                    />
                ) : (
                    <div className="relative flex h-12 w-12 items-center justify-center rounded-xl bg-brand-500 text-lg font-bold text-white">
                        {(branding?.name ?? 'IMDR').slice(0, 1)}
                    </div>
                )}
            </div>
            <div>
                <div className={`text-xl font-bold leading-tight ${light ? 'text-white' : 'text-slate-900'}`}>
                    {branding?.name ?? 'IMDR'}
                </div>
                <div className={`text-xs ${light ? 'text-slate-300' : 'text-slate-500'}`}>
                    Inventory Management System
                </div>
            </div>
        </div>
    );
}

export default function GuestLayout({ children }) {
    const { branding } = usePage().props;

    return (
        <div className="min-h-screen flex bg-slate-50">
            {/* ============ LEFT: animated brand panel ============ */}
            <div className="relative hidden lg:flex lg:w-1/2 flex-col justify-between overflow-hidden bg-gradient-to-br from-slate-950 via-slate-900 to-brand-700 bg-[length:200%_200%] animate-gradient p-12">
                {/* grid pattern */}
                <div
                    className="pointer-events-none absolute inset-0 opacity-[0.07]"
                    style={{
                        backgroundImage:
                            'linear-gradient(#fff 1px, transparent 1px), linear-gradient(90deg, #fff 1px, transparent 1px)',
                        backgroundSize: '44px 44px',
                    }}
                />

                {/* moving blobs */}
                <div className="pointer-events-none absolute -top-20 -right-20 h-80 w-80 rounded-full bg-brand-500/30 blur-3xl animate-blob" />
                <div
                    className="pointer-events-none absolute bottom-0 -left-20 h-96 w-96 rounded-full bg-sky-500/20 blur-3xl animate-blob"
                    style={{ animationDelay: '-6s' }}
                />
                <div
                    className="pointer-events-none absolute top-1/2 left-1/3 h-64 w-64 rounded-full bg-indigo-500/20 blur-3xl animate-blob"
                    style={{ animationDelay: '-11s' }}
                />

                {/* floating cards (wide screens only) */}
                <div className="absolute right-10 top-28 hidden xl:block animate-float-slow">
                    <div className="w-52 rounded-2xl border border-white/15 bg-white/10 p-4 shadow-2xl backdrop-blur-md">
                        <div className="flex items-center justify-between">
                            <span className="text-[10px] font-semibold uppercase tracking-wider text-slate-300">Asset</span>
                            <span className="rounded-full bg-emerald-400/20 px-2 py-0.5 text-[10px] font-medium text-emerald-300">
                                Available
                            </span>
                        </div>
                        <div className="mt-2 text-lg font-bold text-white">IMDR-IT-001</div>
                        <div className="text-xs text-slate-300">Dell Latitude 5440</div>
                        <div className="mt-3 h-1.5 overflow-hidden rounded-full bg-white/10">
                            <div className="h-full w-3/4 rounded-full bg-brand-400" />
                        </div>
                    </div>
                </div>

                <div className="absolute right-24 bottom-36 hidden xl:block animate-float" style={{ animationDelay: '-2s' }}>
                    <div className="rounded-2xl border border-white/15 bg-white/10 p-3 shadow-2xl backdrop-blur-md">
                        <div className="grid grid-cols-7 gap-[3px]">
                            {QR_PATTERN.map((on, i) => (
                                <span key={i} className={`h-2.5 w-2.5 rounded-[2px] ${on ? 'bg-white' : 'bg-white/10'}`} />
                            ))}
                        </div>
                    </div>
                </div>

                <div className="absolute right-6 bottom-72 hidden xl:block animate-float" style={{ animationDelay: '-4s' }}>
                    <div className="flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-medium text-white shadow-xl backdrop-blur-md">
                        <span className="relative flex h-2 w-2">
                            <span className="absolute inline-flex h-full w-full rounded-full bg-amber-400 animate-pulse-ring" />
                            <span className="relative inline-flex h-2 w-2 rounded-full bg-amber-400" />
                        </span>
                        Issued to user
                    </div>
                </div>

                {/* content */}
                <div className="relative animate-fade-in">
                    <Brand branding={branding} light />
                </div>

                <div className="relative">
                    <h2
                        className="max-w-md text-4xl font-bold leading-tight text-white animate-fade-up"
                        style={{ animationDelay: '0.15s' }}
                    >
                        Know what you own, where it is, and who has it.
                    </h2>
                    <p
                        className="mt-4 max-w-md text-slate-300 animate-fade-up"
                        style={{ animationDelay: '0.3s' }}
                    >
                        One place to manage assets, stock, assignments, maintenance and reports.
                    </p>

                    <ul className="mt-10 space-y-5">
                        {FEATURES.map((f, i) => (
                            <li
                                key={f.title}
                                className="flex gap-4 animate-fade-up"
                                style={{ animationDelay: `${0.5 + i * 0.15}s` }}
                            >
                                <span className="mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand-500/30 text-brand-100">
                                    <svg className="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fillRule="evenodd" d="M16.7 5.3a1 1 0 010 1.4l-7.5 7.5a1 1 0 01-1.4 0L3.3 9.7a1 1 0 111.4-1.4l3.8 3.8 6.8-6.8a1 1 0 011.4 0z" clipRule="evenodd" />
                                    </svg>
                                </span>
                                <div>
                                    <div className="font-medium text-white">{f.title}</div>
                                    <div className="text-sm text-slate-400">{f.text}</div>
                                </div>
                            </li>
                        ))}
                    </ul>
                </div>

                <div className="relative text-xs text-slate-500 animate-fade-in" style={{ animationDelay: '1.2s' }}>
                    &copy; {new Date().getFullYear()} {branding?.name ?? 'IMDR'}. All rights reserved.
                </div>
            </div>

            {/* ============ RIGHT: form area ============ */}
            <div className="relative flex w-full lg:w-1/2 items-center justify-center overflow-hidden px-6 py-12">
                {/* soft background blobs for the light side */}
                <div className="pointer-events-none absolute -top-24 -right-24 h-72 w-72 rounded-full bg-brand-100 blur-3xl opacity-70 animate-blob" />
                <div
                    className="pointer-events-none absolute -bottom-24 -left-24 h-72 w-72 rounded-full bg-sky-100 blur-3xl opacity-70 animate-blob"
                    style={{ animationDelay: '-8s' }}
                />

                <div className="relative w-full max-w-md">
                    <div className="mb-8 lg:hidden animate-fade-up">
                        <Brand branding={branding} />
                    </div>
                    <div
                        className="rounded-2xl border border-slate-200/80 bg-white/90 p-8 shadow-xl shadow-slate-200/60 backdrop-blur animate-fade-up"
                        style={{ animationDelay: '0.2s' }}
                    >
                        {children}
                    </div>
                </div>
            </div>
        </div>
    );
}