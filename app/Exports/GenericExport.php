<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

/**
 * A single reusable export class fed by whatever report data the
 * ReportController collects, so every report (Inventory, Low Stock,
 * Assignment History, etc.) can be exported to Excel without a bespoke
 * export class per report.
 */
class GenericExport implements FromCollection, WithHeadings, WithTitle
{
    public function __construct(
        protected array $rows,
        protected array $headings,
        protected string $title = 'Report',
    ) {
    }

    public function collection()
    {
        return collect($this->rows);
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function title(): string
    {
        return $this->title;
    }
}
