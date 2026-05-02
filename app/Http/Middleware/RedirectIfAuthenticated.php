<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;

/**
 * @see Illuminate\Auth\Middleware\RedirectIfAuthenticated copy paste & override guest middleware
 */
class RedirectIfAuthenticated extends \Illuminate\Auth\Middleware\RedirectIfAuthenticated
{
    /**
     * Get the path the user should be redirected to when they are authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return in_array($request->segment(1), [
            'admin',
            'franchise',
            'customer_service',
        ]) ? route($request->segment(1).'.dashboard')
            : $this->defaultRedirectUri();
    }
}
