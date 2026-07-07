<?php

namespace App\Http\Controllers\Registrar;

use App\Data\BusinessItemData;
use App\Data\TaxSubCategoryData;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class BusinessItemLookupController extends Controller
{
    public function __invoke(): View
    {
        return view('registrar.lookup', [
            'items'         => BusinessItemData::all(),
            'majorGroups'   => BusinessItemData::majorGroups(),
            'subCategories' => TaxSubCategoryData::all(),
        ]);
    }
}
