import { useForm, Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import FormInput from '@/Components/FormInput';

export default function Create() {
    const { data, setData, post, processing, errors } = useForm({
        full_name: '', email: '', phone: '', password: '', status: 'active',
    });

    function submit(e) {
        e.preventDefault();
        post(route('settings.admins.store'));
    }

    return (
        <AuthenticatedLayout header="Create Admin">
            <Head title="Create Admin" />
            <form onSubmit={submit} className="max-w-lg bg-white rounded-xl border border-gray-200 p-6 space-y-4">
                <FormInput label="Full Name" value={data.full_name} onChange={(e) => setData('full_name', e.target.value)} error={errors.full_name} required autoFocus />
                <FormInput label="Email" type="email" value={data.email} onChange={(e) => setData('email', e.target.value)} error={errors.email} required />
                <FormInput label="Phone" value={data.phone} onChange={(e) => setData('phone', e.target.value)} error={errors.phone} />
                <FormInput label="Password" type="password" value={data.password} onChange={(e) => setData('password', e.target.value)} error={errors.password} required />
                <button type="submit" disabled={processing} className="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700 disabled:opacity-50">
                    Create Admin
                </button>
            </form>
        </AuthenticatedLayout>
    );
}
