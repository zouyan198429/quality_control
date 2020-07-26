<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
     * 这些 URI 将免受 CSRF 验证
     * @var array
     */
    protected $except = [
//        'stripe/*',
//        'http://example.com/foo/bar',
//        'http://example.com/foo/*',
    ];
}
