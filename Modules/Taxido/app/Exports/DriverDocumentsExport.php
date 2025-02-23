<?php

namespace Modules\Taxido\Exports;

use Modules\Taxido\Models\DriverDocument;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class DriverDocumentsExport implements FromCollection, WithMapping, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $driverDocuments = DriverDocument::whereNull('deleted_at')->latest('created_at');
        return $this->filter($driverDocuments, request());
    }

    /**
     * Specify the columns for the export.
     *
     * @return array
     */
    public function columns(): array
    {
        return [
            'id',
            'driver_id',
            'document_id',
            'document_no',
            'document_image_id',
            'note',
            'status',
        ];
    }
    public function map($driverDocument): array
    {
        return [
            $driverDocument->id,
            $driverDocument->driver ? $driverDocument->driver->name : 'N/A', 
            $driverDocument->document->pluck('name')->implode(','),
            $driverDocument->document_no,
            $driverDocument->document_image?->original_url,
            $driverDocument->note,
            $driverDocument->status,
        ];
    }
    /**
     * Get the headings for the export file.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Driver',
            'Document',
            'Document No',
            'Document Image',
            'Note',
            'Status',
        ];
    }

    public function filter($driverDocuments, $request)
    {
        return $driverDocuments->get();
    }
}
