<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\Rule;

class RegistrationController extends Controller
{
    public function show($eventId, Request $request)
    {
        $event = Event::findOrFail($eventId);

        // Lingua
        $lang = $request->get('lang','it');
        App::setLocale($lang);

        // Traduzioni per il Blade
        $translations = [
            'it'=>[
                'first_name'=>'Nome','last_name'=>'Cognome','email'=>'Email','phone'=>'Telefono (+Prefisso)',
                'birth_date'=>'Data di nascita','gender'=>'Genere','nationality'=>'Nazionalità',
                'esn_card'=>'ESN Card (12 caratteri)','document_type'=>'Tipo Documento','document_number'=>'Numero Documento',
                'male'=>'Maschio','female'=>'Femmina','prefer_not'=>'Preferisco non specificare','select'=>'Seleziona',
                'required'=>'Campi obbligatori','submit'=>'Iscriviti',
                'id_card'=>'Carta d\'identità','passport'=>'Passaporto','driver_license'=>'Patente di guida',
                'success'=>'Registrazione completata'
            ],
            'en'=>[
                'first_name'=>'First Name','last_name'=>'Last Name','email'=>'Email','phone'=>'Phone (+Prefix)',
                'birth_date'=>'Birth Date','gender'=>'Gender','nationality'=>'Nationality',
                'esn_card'=>'ESN Card (12 characters)','document_type'=>'Document Type','document_number'=>'Document Number',
                'male'=>'Male','female'=>'Female','prefer_not'=>'Prefer not to say','select'=>'Select',
                'required'=>'Required fields','submit'=>'Register',
                'id_card'=>'Identity Card','passport'=>'Passport','driver_license'=>'Driver License',
                'success'=>'Registration completed'
            ]
        ];

        return view('register', [
            'event'=>$event,
            'lang'=>$lang,
            't'=>$translations[$lang]
        ]);
    }

    public function store(Request $request)
    {
        $lang = $request->get('lang','it');
        App::setLocale($lang);

        $validated = $request->validate([
            'event_id'=>'required|exists:events,id',
            'first_name'=>'required|string|max:255',
            'last_name'=>'required|string|max:255',
            'email'=>'required|email|max:255',
            'phone'=>'required|string|regex:/^\+\d{6,15}$/',
            'birth_date'=>'required|date|before_or_equal:'.now()->subYears(18)->format('Y-m-d'),
            'gender'=>['required',Rule::in(['M','F','N'])],
            'nationality'=>'required|string|max:100',
            'has_esn_card'=>'required|string|size:12',
            'document_type'=>['required',Rule::in(['id','passport','driver_license'])],
            'document_number'=>'required|string|max:50'
        ]);

        // Capitalizza Nome e Cognome
        $validated['first_name'] = $this->capitalizeWords($validated['first_name']);
        $validated['last_name'] = $this->capitalizeWords($validated['last_name']);

        // Email minuscola, ESN Card maiuscola
        $validated['email'] = strtolower($validated['email']);
        $validated['has_esn_card'] = strtoupper($validated['has_esn_card']);

        Registration::create($validated);

        return redirect()->back()->with('success', __('success'));
    }

    private function capitalizeWords($string)
    {
        $words = explode(' ', $string);
        $words = array_map(fn($w)=>ucfirst(strtolower($w)), $words);
        return implode(' ', $words);
    }
}
