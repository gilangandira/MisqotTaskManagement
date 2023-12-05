<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CompressImages
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
        if ($response->headers->has('Content-Type') && str_contains($response->headers->get('Content-Type'), 'image')) {
            // Kompresi gambar menggunakan Intervention Image
            $image = Image::make($response->getContent());

            // Batas ukuran gambar
            $maxSizeKB = 500; // Sesuaikan dengan kebutuhan

            // Validasi ukuran gambar setelah kompresi
            if ($image->filesize() > $maxSizeKB * 1024) {
                // Jika ukuran gambar melebihi batas, kompres ulang dengan kualitas yang lebih rendah
                $response->setContent($image->encode('jpg', 60)); // Sesuaikan dengan kebutuhan
                $response->headers->set('Content-Type', 'image/jpeg');
            }
        }

        return $response;
    }
}
