<?php

namespace App\Http\Controllers\Registrar;

use App\Data\BusinessItemData;
use App\Data\TaxSubCategoryData;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class BusinessItemComposeController extends Controller
{
    public function __invoke(): View
    {
        return view('registrar.compose', [
            'items'         => BusinessItemData::all(),
            'majorGroups'   => BusinessItemData::majorGroups(),
            'subCategories' => TaxSubCategoryData::all(),
        ]);
    }
}
