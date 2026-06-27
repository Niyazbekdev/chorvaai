<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function switch(Request $request, string $locale): RedirectResponse
    {
        if (!in_array($locale, ['uz', 'ru'])) {
            abort(404);
        }

        session(['locale' => $locale]);

        return redirect()->back();
    }
}
