import { useForm, Head } from '@inertiajs/react';
import GuestLayout from '@/Layouts/GuestLayout';
import FormInput from '@/Components/FormInput';

export default function Login({ status, canResetPassword }) {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    function submit(e) {
        e.preventDefault();
        post(route('login'));
    }

    return (
        <GuestLayout>
            <Head title="Log in" />

            {status && (
                <div className="mb-4 rounded-lg bg-green-50 border border-green-200 px-4 py-2 text-sm text-green-800">
                    {status}
                </div>
            )}

            <form onSubmit={submit} className="space-y-4">
                <FormInput
                    label="Email"
                    type="email"
                    value={data.email}
                    onChange={(e) => setData('email', e.target.value)}
                    error={errors.email}
                    required
                    autoFocus
                />
                <FormInput
                    label="Password"
                    type="password"
                    value={data.password}
                    onChange={(e) => setData('password', e.target.value)}
                    error={errors.password}
                    required
                />

                <label className="flex items-center gap-2 text-sm text-gray-600">
                    <input
                        type="checkbox"
                        checked={data.remember}
                        onChange={(e) => setData('remember', e.target.checked)}
                        className="rounded border-gray-300 text-brand-600 focus:ring-brand-500"
                    />
                    Remember me
                </label>

                <button
                    type="submit"
                    disabled={processing}
                    className="w-full rounded-lg bg-brand-600 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 disabled:opacity-50"
                >
                    Log in
                </button>

                {canResetPassword && (
                    <a href={route('password.request')} className="block text-center text-sm text-brand-600 hover:underline">
                        Forgot your password?
                    </a>
                )}
            </form>

            <p className="mt-6 text-center text-xs text-gray-400">
                Accounts are created by a Super Admin or Admin — there is no public sign-up.
            </p>
        </GuestLayout>
    );
}
