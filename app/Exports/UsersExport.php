<?php

namespace App\Exports;

use App\Models\User;
use App\Enums\RoleEnum;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class UsersExport implements FromCollection,WithMapping,WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return User::role(RoleEnum::USER)->Where('system_reserve',false)->whereNull('deleted_at')->latest('created_at')->get();

    }
    public function columns(): array
    {
        return [
            'id',
            'name',
            'username',
            'email',
            'country_code',
            'phone',
            'status',
            'profile_image_id',
            'password',
            'created_at'
        ];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->username,
            $user->email,
            $user->country_code,
            $user->phone,
            $user->status,
            $user->profile_image?->original_url,
            null,
            $user->created_at
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
            'Name',
            'UserName',
            'Email',
            'Country Code',
            'Phone',
            'Status',
            'Profile Image',
            'Password',
            'Created At'
        ];
    }

    public function filter($users, $request)
    {
        return $users->get();
    }
}