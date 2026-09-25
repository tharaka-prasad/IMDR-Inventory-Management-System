import { useForm, Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import FormInput from '@/Components/FormInput';

export default function Edit({ user }) {
    const profileForm = useForm({
        full_name: user.full_name,
        email: user.email,
        phone: user.phone ?? '',
    });

    const passwordForm = useForm({
        current_password: '',
        password: '',
        password_confirmation: '',
    });

    function submitProfile(e) {
        e.preventDefault();
        profileForm.patch(route('profile.update'));
    }

    function submitPassword(e) {
        e.preventDefault();
        passwordForm.put(route('profile.password'), {
            onSuccess: () => passwordForm.reset(),
        });
    }

    return (
        <AuthenticatedLayout header="My Profile">
            <Head title="My Profile" />

            <div className="grid gap-6 max-w-2xl">
                <div className="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 className="text-sm font-semibold text-gray-900 mb-4">Profile Information</h2>
                    <form onSubmit={submitProfile} className="space-y-4">
                        <FormInput label="Full Name" value={profileForm.data.full_name} onChange={(e) => profileForm.setData('full_name', e.target.value)} error={profileForm.errors.full_name} required />
                        <FormInput label="Email" type="email" value={profileForm.data.email} onChange={(e) => profileForm.setData('email', e.target.value)} error={profileForm.errors.email} required />
                        <FormInput label="Phone" value={profileForm.data.phone} onChange={(e) => profileForm.setData('phone', e.target.value)} error={profileForm.errors.phone} />
                        <button type="submit" disabled={profileForm.processing} className="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700 disabled:opacity-50">
                            Save
                        </button>
                    </form>
                </div>

                <div className="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 className="text-sm font-semibold text-gray-900 mb-4">Update Password</h2>
                    <form onSubmit={submitPassword} className="space-y-4">
                        <FormInput label="Current Password" type="password" value={passwordForm.data.current_password} onChange={(e) => passwordForm.setData('current_password', e.target.value)} error={passwordForm.errors.current_password} required />
                        <FormInput label="New Password" type="password" value={passwordForm.data.password} onChange={(e) => passwordForm.setData('password', e.target.value)} error={passwordForm.errors.password} required />
                        <FormInput label="Confirm New Password" type="password" value={passwordForm.data.password_confirmation} onChange={(e) => passwordForm.setData('password_confirmation', e.target.value)} error={passwordForm.errors.password_confirmation} required />
                        <button type="submit" disabled={passwordForm.processing} className="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700 disabled:opacity-50">
                            Update Password
                        </button>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
