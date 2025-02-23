<?php

namespace Modules\Taxido\Imports;

use App\Exceptions\ExceptionHandler;
use Maatwebsite\Excel\Concerns\ToModel;
use Modules\Taxido\Models\DriverDocument;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class DriverDocumentImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    private $driverDocuments = [];

    public function rules(): array
    {

        return [
            'driver_id' => ['required', 'exists:drivers,id'],
            'document_id' => ['required', 'exists:documents,id'],
            'document_no' => ['required', 'string', 'max:255'],
            'document_image' => ['nullable', 'url'],
            'status' => ['required', 'integer'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'driver_id.exists' => __('validation.driver_id_invalid'),
            'document_id.exists' => __('validation.document_id_invalid'),
            'document_no.required' => __('validation.document_no_required'),
            'document_image.url' => __('validation.document_image_url_invalid'),
            'status.required' => __('validation.status_field_required'),
        ];
    }

    /**
     * @param \Throwable $e
     */
    public function onError(\Throwable $e)
    {
        throw new ExceptionHandler($e->getMessage(), 422);
    }

    public function getImportedDriverDocuments()
    {
        return $this->driverDocuments;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $driverDocument = new DriverDocument([
            'driver_id' => $row['driver_id'],
            'document_id' => $row['document_id'],
            'document_no' => $row['document_no'],
            'status' => (int)$row['status'],
            'created_by_id' => getCurrentUserId(),
        ]);

        $driverDocument->save();

        if (isset($row['document_image'])) {
            $media = $driverDocument->addMediaFromUrl($row['document_image'])->toMediaCollection('document_image');
            $media->save();
            $driverDocument->document_image_id = $media->id;
            $driverDocument->save();
        }

        $this->driverDocuments[] = [
            'id' => $driverDocument->id,
            'driver_id' => $driverDocument->driver_id,
            'document_id' => $driverDocument->document_id,
            'document_no' => $driverDocument->document_no,
            'status' => $driverDocument->status,
            'document_image' => $driverDocument->document_image,
        ];

        return $driverDocument;
    }
}
