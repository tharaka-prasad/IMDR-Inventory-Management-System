import { useForm, Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import FormInput from '@/Components/FormInput';

export default function Index({ settings }) {
    const { data, setData, post, processing, errors } = useForm({
        institute_name: settings.institute_name ?? '',
        asset_prefix: settings.asset_prefix ?? '',
        qr_prefix: settings.qr_prefix ?? '',
        currency: settings.currency ?? '',
        logo: null,
        _method: 'post',
    });

    function submit(e) {
        e.preventDefault();
        // File upload via Inertia needs a real multipart POST.
        post(route('settings.system.update'), { forceFormData: true });
    }

    return (
        <AuthenticatedLayout header="System Settings">
            <Head title="System Settings" />

            <form onSubmit={submit} className="max-w-lg bg-white rounded-xl border border-gray-200 p-6 space-y-4">
                <FormInput label="Institute Name" value={data.institute_name} onChange={(e) => setData('institute_name', e.target.value)} error={errors.institute_name} required />

                {settings.logo_path && (
                    <img src={`/storage/${settings.logo_path}`} alt="Current logo" className="h-12" />
                )}
                <FormInput
                    label="Logo Upload"
                    type="file"
                    accept="image/*"
                    onChange={(e) => setData('logo', e.target.files[0])}
                    error={errors.logo}
                />

                <FormInput label="Asset Prefix (e.g. IMDR)" value={data.asset_prefix} onChange={(e) => setData('asset_prefix', e.target.value.toUpperCase())} error={errors.asset_prefix} required />
                <FormInput label="QR Prefix (e.g. IMDR-QR)" value={data.qr_prefix} onChange={(e) => setData('qr_prefix', e.target.value.toUpperCase())} error={errors.qr_prefix} required />
                <FormInput label="Currency (e.g. LKR)" value={data.currency} onChange={(e) => setData('currency', e.target.value.toUpperCase())} error={errors.currency} required />

                <button type="submit" disabled={processing} className="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700 disabled:opacity-50">
                    Save Settings
                </button>
            </form>
        </AuthenticatedLayout>
    );
}
