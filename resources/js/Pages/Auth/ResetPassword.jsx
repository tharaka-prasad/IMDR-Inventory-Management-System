import { useForm, Head } from '@inertiajs/react';
import GuestLayout from '@/Layouts/GuestLayout';
import FormInput from '@/Components/FormInput';

export default function ResetPassword({ token, email }) {
    const { data, setData, post, processing, errors } = useForm({
        token,
        email,
        password: '',
        password_confirmation: '',
    });

    function submit(e) {
        e.preventDefault();
        post(route('password.store'));
    }

    return (
        <GuestLayout>
            <Head title="Reset Password" />

            <form onSubmit={submit} className="space-y-4">
                <FormInput label="Email" type="email" value={data.email} onChange={(e) => setData('email', e.target.value)} error={errors.email} required />
                <FormInput label="New Password" type="password" value={data.password} onChange={(e) => setData('password', e.target.value)} error={errors.password} required autoFocus />
                <FormInput label="Confirm Password" type="password" value={data.password_confirmation} onChange={(e) => setData('password_confirmation', e.target.value)} error={errors.password_confirmation} required />

                <button
                    type="submit"
                    disabled={processing}
                    className="w-full rounded-lg bg-brand-600 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 disabled:opacity-50"
                >
                    Reset Password
                </button>
            </form>
        </GuestLayout>
    );
}
