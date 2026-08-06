<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationProfile extends Model
{
    use HasFactory;

    protected $table = 'organization_profiles';

    protected $fillable = [
        'organization_name',
        'address',
        'chairperson',
        'headquarters_treasurer',
        'blood_donation_unit_treasurer',
        'financial_period_start',
        'financial_period_end',
        'fiscal_year',
        'manual_book_link',
    ];

    protected $casts = [
        'financial_period_start' => 'date',
        'financial_period_end' => 'date',
        'fiscal_year' => 'integer',
    ];
}
