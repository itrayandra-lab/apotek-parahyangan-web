<?php

namespace Database\Seeders;

use App\Models\ExpertQuote;
use Illuminate\Database\Seeder;

class ExpertQuoteSeeder extends Seeder
{
    public function run(): void
    {
        ExpertQuote::query()->updateOrCreate(
            ['author_name' => 'Apt. Cahya Khairani, S.Si, M.Farm'],
            [
                'quote' => 'True beauty is biological harmony. We do not just treat symptoms; we recalibrate the skin\'s ecosystem to function at its peak potential.',
                'author_title' => 'LEAD FORMULATOR',
                'is_active' => true,
            ]
        );
    }
}
