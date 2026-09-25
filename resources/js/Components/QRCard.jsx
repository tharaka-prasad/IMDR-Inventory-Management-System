export default function QRCard({ qrCode, assetCode, imageUrl }) {
    return (
        <div className="bg-white rounded-xl border border-gray-200 p-5 text-center shadow-sm">
            {imageUrl ? (
                <img src={imageUrl} alt={qrCode} className="mx-auto w-36 h-36" />
            ) : (
                <div className="mx-auto w-36 h-36 bg-gray-100 rounded-lg flex items-center justify-center text-xs text-gray-400">
                    QR pending
                </div>
            )}
            <div className="mt-3 text-sm font-semibold text-gray-900">{assetCode}</div>
            <div className="text-xs text-gray-500">{qrCode}</div>
        </div>
    );
}
