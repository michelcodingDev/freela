<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    /**
     * Termos de uso da plataforma.
     */
    public function terms()
    {
        return view('pages.terms');
    }

    /**
     * Política de privacidade.
     */
    public function privacy()
    {
        return view('pages.privacy');
    }

    /**
     * Central de suporte e contato.
     */
    public function support()
    {
        return view('pages.support');
    }
}
