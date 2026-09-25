import { useForm, Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import FormInput from '@/Components/FormInput';
import SelectInput from '@/Components/SelectInput';

export default function Edit({ admin }) {
    const { data, setData, put, processing, errors } = useForm({
        full_name: admin.full_name,
        email: admin.email,
        phone: admin.phone ?? '',
        password: '',
        status: admin.status,
    });

    function submit(e) {
        e.preventDefault();
        put(route('settings.admins.update', admin.id));
    }

    return (
        <AuthenticatedLayout header={`Edit ${admin.full_name}`}>
            <Head title="Edit Admin" />
            <form onSubmit={submit} className="max-w-lg bg-white rounded-xl border border-gray-200 p-6 space-y-4">
                <FormInput label="Full Name" value={data.full_name} onChange={(e) => setData('full_name', e.target.value)} error={errors.full_name} required />
                <FormInput label="Email" type="email" value={data.email} onChange={(e) => setData('email', e.target.value)} error={errors.email} required />
                <FormInput label="Phone" value={data.phone} onChange={(e) => setData('phone', e.target.value)} error={errors.phone} />
                <FormInput label="New Password (optional)" type="password" value={data.password} onChange={(e) => setData('password', e.target.value)} error={errors.password} />
                <SelectInput label="Status" value={data.status} onChange={(e) => setData('status', e.target.value)} error={errors.status} required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </SelectInput>
                <button type="submit" disabled={processing} className="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700 disabled:opacity-50">
                    Save Changes
                </button>
            </form>
        </AuthenticatedLayout>
    );
}
