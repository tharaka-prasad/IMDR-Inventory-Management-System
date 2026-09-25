export default function GuestLayout({ children }) {
    return (
        <div className="min-h-screen flex items-center justify-center bg-slate-900 px-4">
            <div className="w-full max-w-md">
                <div className="text-center mb-8">
                    <div className="text-2xl font-bold text-white">IMDR</div>
                    <div className="text-slate-400 text-sm">Inventory Management System</div>
                </div>
                <div className="bg-white rounded-xl shadow-lg p-8">{children}</div>
            </div>
        </div>
    );
}
