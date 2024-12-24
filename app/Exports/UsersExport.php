<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
{
    protected $users;

    public function __construct($users)
    {
        $this->users = $users;
    }

    public function collection()
    {
        return $this->users->map(function ($user) {
            return [
                'ID' => $user->id,
                'Name (EN)' => $user->getTranslation('name', 'en'),
                'Name (AR)' => $user->getTranslation('name', 'ar'),
                'Email' => $user->email,
                'Phone' => $user->phone,
                'Contact Email' => $user->contact_email,
                'Type' => $user->type->name,
                'Start Date' => $user->created_at->format('Y-m-d H:i:s'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name (EN)',
            'Name (AR)',
            'Email',
            'Phone',
            'Contact Email',
            'Type',
            'Start Date',
        ];
    }
}
