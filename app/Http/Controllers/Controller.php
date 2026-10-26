<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Pesan error yang aman untuk API.
     * Saat APP_DEBUG=false (production), detail exception disembunyikan
     * agar tidak membocorkan info sistem ke peretas.
     */
    protected function errorMessage(\Throwable $e): string
    {
        return config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan pada server.';
    }
}
