import { useState } from 'react';
import { useForm, Head, Link } from '@inertiajs/react';
import GuestLayout from '@/Layouts/GuestLayout';

function MailIcon() {
    return (
        <svg className="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8">
            <rect x="3" y="5" width="18" height="14" rx="2" />
            <path d="m3 7 9 6 9-6" strokeLinecap="round" strokeLinejoin="round" />
        </svg>
    );
}

function LockIcon() {
    return (
        <svg className="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8">
            <rect x="5" y="11" width="14" height="9" rx="2" />
            <path d="M8 11V8a4 4 0 118 0v3" strokeLinecap="round" />
        </svg>
    );
}

export default function Login({ status, canResetPassword }) {
    const [showPassword, setShowPassword] = useState(false);
    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const hasError = Boolean(errors.email || errors.password);

    function submit(e) {
        e.preventDefault();
        post(route('login'));
    }

    const inputBase =
        'peer w-full rounded-xl border bg-slate-50 py-3 pl-11 pr-4 text-sm text-slate-900 placeholder-slate-400 transition duration-200 focus:bg-white focus:outline-none focus:ring-4';

    return (
        <GuestLayout>
            <Head title="Log in" />

            <div className="mb-7">
                <h1 className="text-2xl font-bold text-slate-900">Welcome back</h1>
                <p className="mt-1 text-sm text-slate-500">Sign in to continue to your dashboard.</p>
            </div>

            {status && (
                <div className="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-2 text-sm text-green-800">
                    {status}
                </div>
            )}

            {/* key changes when errors change, so the shake animation replays */}
            <form
                key={hasError ? JSON.stringify(errors) : 'ok'}
                onSubmit={submit}
                className={`space-y-5 ${hasError ? 'animate-shake' : ''}`}
            >
                {/* Email */}
                <div>
                    <label className="mb-1.5 block text-sm font-medium text-slate-700">Email</label>
                    <div className="relative">
                        <input
                            type="email"
                            value={data.email}
                            onChange={(e) => setData('email', e.target.value)}
                            placeholder="you@imdr.lk"
                            required
                            autoFocus
                            className={`${inputBase} ${
                                errors.email
                                    ? 'border-red-400 focus:border-red-500 focus:ring-red-100'
                                    : 'border-slate-200 focus:border-brand-500 focus:ring-brand-100'
                            }`}
                        />
                        <span className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 transition peer-focus:text-brand-500">
                            <MailIcon />
                        </span>
                    </div>
                    {errors.email && <p className="mt-1.5 text-xs text-red-600">{errors.email}</p>}
                </div>

                {/* Password */}
                <div>
                    <label className="mb-1.5 block text-sm font-medium text-slate-700">Password</label>
                    <div className="relative">
                        <input
                            type={showPassword ? 'text' : 'password'}
                            value={data.password}
                            onChange={(e) => setData('password', e.target.value)}
                            placeholder="Enter your password"
                            required
                            className={`${inputBase} pr-16 ${
                                errors.password
                                    ? 'border-red-400 focus:border-red-500 focus:ring-red-100'
                                    : 'border-slate-200 focus:border-brand-500 focus:ring-brand-100'
                            }`}
                        />
                        <span className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400 transition peer-focus:text-brand-500">
                            <LockIcon />
                        </span>
                        <button
                            type="button"
                            onClick={() => setShowPassword((s) => !s)}
                            className="absolute inset-y-0 right-0 px-4 text-xs font-semibold text-slate-500 transition hover:text-brand-600"
                        >
                            {showPassword ? 'Hide' : 'Show'}
                        </button>
                    </div>
                    {errors.password && <p className="mt-1.5 text-xs text-red-600">{errors.password}</p>}
                </div>

                <div className="flex items-center justify-between">
                    <label className="flex cursor-pointer items-center gap-2 text-sm text-slate-600">
                        <input
                            type="checkbox"
                            checked={data.remember}
                            onChange={(e) => setData('remember', e.target.checked)}
                            className="rounded border-slate-300 text-brand-600 focus:ring-brand-500"
                        />
                        Remember me
                    </label>

                    {canResetPassword && (
                        <Link
                            href={route('password.request')}
                            className="text-sm font-medium text-brand-600 transition hover:text-brand-700 hover:underline"
                        >
                            Forgot password?
                        </Link>
                    )}
                </div>

                {/* Submit with shimmer + moving arrow */}
                <button
                    type="submit"
                    disabled={processing}
                    className="group relative w-full overflow-hidden rounded-xl bg-brand-600 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-500/30 transition duration-200 hover:-translate-y-0.5 hover:bg-brand-700 hover:shadow-xl hover:shadow-brand-500/40 active:translate-y-0 disabled:opacity-70 disabled:hover:translate-y-0"
                >
                    <span className="pointer-events-none absolute inset-y-0 left-0 w-1/3 bg-white/25 animate-shimmer" />
                    <span className="relative flex items-center justify-center gap-2">
                        {processing ? (
                            <>
                                <svg className="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" className="opacity-25" />
                                    <path d="M4 12a8 8 0 018-8" stroke="currentColor" strokeWidth="4" strokeLinecap="round" className="opacity-90" />
                                </svg>
                                Signing in...
                            </>
                        ) : (
                            <>
                                Sign in
                                <svg
                                    className="h-4 w-4 transition-transform duration-200 group-hover:translate-x-1"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    strokeWidth="2"
                                >
                                    <path d="M5 12h14m-6-6 6 6-6 6" strokeLinecap="round" strokeLinejoin="round" />
                                </svg>
                            </>
                        )}
                    </span>
                </button>
            </form>

            <p className="mt-6 text-center text-xs text-slate-400">
                Accounts are created by an administrator. There is no public sign-up.
            </p>
        </GuestLayout>
    );
}