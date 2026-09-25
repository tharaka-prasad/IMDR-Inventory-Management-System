import { useForm, Head } from '@inertiajs/react';
import GuestLayout from '@/Layouts/GuestLayout';
import FormInput from '@/Components/FormInput';

export default function ForgotPassword({ status }) {
    const { data, setData, post, processing, errors } = useForm({ email: '' });

    function submit(e) {
        e.preventDefault();
        post(route('password.email'));
    }

    return (
        <GuestLayout>
            <Head title="Forgot Password" />

            <p className="mb-4 text-sm text-gray-600">
                Enter your email and we'll send you a password reset link.
            </p>

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

                <button
                    type="submit"
                    disabled={processing}
                    className="w-full rounded-lg bg-brand-600 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 disabled:opacity-50"
                >
                    Email Password Reset Link
                </button>
            </form>
        </GuestLayout>
    );
}
