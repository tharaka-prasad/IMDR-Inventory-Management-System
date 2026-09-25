import { useForm, Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import FormInput from '@/Components/FormInput';

export default function Edit({ targetUser }) {
    const { data, setData, put, processing, errors } = useForm({
        full_name: targetUser.full_name,
        email: targetUser.email,
        phone: targetUser.phone ?? '',
        password: '',
        password_confirmation: '',
    });

    function submit(e) {
        e.preventDefault();
        put(route('settings.users.update', targetUser.id));
    }

    return (
        <AuthenticatedLayout header={`Edit ${targetUser.full_name}`}>
            <Head title="Edit Assignee" />
            <form onSubmit={submit} className="max-w-lg bg-white rounded-xl border border-gray-200 p-6 space-y-4">
                <FormInput label="Full Name" value={data.full_name} onChange={(e) => setData('full_name', e.target.value)} error={errors.full_name} required />
                <FormInput label="Email" type="email" value={data.email} onChange={(e) => setData('email', e.target.value)} error={errors.email} required />
                <FormInput label="Phone" value={data.phone} onChange={(e) => setData('phone', e.target.value)} error={errors.phone} />
                <FormInput label="New Password (optional)" type="password" value={data.password} onChange={(e) => setData('password', e.target.value)} error={errors.password} />
                <FormInput label="Confirm New Password" type="password" value={data.password_confirmation} onChange={(e) => setData('password_confirmation', e.target.value)} error={errors.password_confirmation} />
                <button type="submit" disabled={processing} className="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700 disabled:opacity-50">
                    Save Changes
                </button>
            </form>
        </AuthenticatedLayout>
    );
}
