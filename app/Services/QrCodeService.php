<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    /**
     * Build a unique QR code value for an asset, e.g. IMDR-QR-8f3ac1.
     * The QR image itself encodes the asset_code so scanning it in the
     * Inventory search box resolves the exact asset.
     */
    public function generateValue(): string
    {
        $settings = SystemSetting::current();
        $prefix = $settings->qr_prefix ?: 'IMDR-QR';

        do {
            $value = sprintf('%s-%s', $prefix, Str::upper(Str::random(6)));
        } while (Inventory::withTrashed()->where('qr_code', $value)->exists());

        return $value;
    }

    /**
     * Render and store the QR PNG for an asset, returns the storage path.
     */
    public function generateImage(Inventory $inventory): string
    {
        $png = QrCode::format('png')->size(300)->generate($inventory->asset_code);
        $path = "qrcodes/{$inventory->qr_code}.png";
        Storage::disk('public')->put($path, $png);

        return $path;
    }
}
