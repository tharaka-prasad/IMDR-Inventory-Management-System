import { useEffect, useState } from 'react';
import { usePage } from '@inertiajs/react';
import Sidebar from './Sidebar';

export default function AuthenticatedLayout({ header, children }) {
    const { flash } = usePage().props;
    const [message, setMessage] = useState(null);

    useEffect(() => {
        if (flash?.success) setMessage({ type: 'success', text: flash.success });
        else if (flash?.error) setMessage({ type: 'error', text: flash.error });
        else setMessage(null);
    }, [flash]);

    return (
        <div className="flex min-h-screen bg-gray-50">
            <Sidebar />

            <div className="flex-1 flex flex-col min-w-0">
                {header && (
                    <header className="bg-white border-b border-gray-200 px-8 py-5">
                        <h1 className="text-xl font-semibold text-gray-900">{header}</h1>
                    </header>
                )}

                {message && (
                    <div
                        className={`mx-8 mt-4 rounded-lg px-4 py-3 text-sm ${
                            message.type === 'success'
                                ? 'bg-green-50 text-green-800 border border-green-200'
                                : 'bg-red-50 text-red-800 border border-red-200'
                        }`}
                    >
                        {message.text}
                    </div>
                )}

                <main className="flex-1 p-8">{children}</main>
            </div>
        </div>
    );
}
