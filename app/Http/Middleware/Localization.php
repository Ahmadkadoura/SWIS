<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $lang = $request->header('accept-language');

        if ($lang != null) {
            // استخراج اللغة الأساسية فقط (مثل en أو en_US)
            $lang = explode(',', $lang)[0]; // أخذ الجزء الأول فقط قبل الفاصلة
            $lang = explode(';', $lang)[0]; // إزالة أي عوامل q-factor

            // التحقق من صحة اللغة
            if (preg_match('/^[a-z]{2}(-[A-Z]{2})?$/', $lang)) {
                session()->put('local', $lang);
                App::setLocale($lang);
            }
        }

        return $next($request);
    }
}
