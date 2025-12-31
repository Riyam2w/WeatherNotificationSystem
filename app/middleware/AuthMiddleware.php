<?php
declare(strict_types=1);

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(): void
    {
        Auth::check(); 
        // AJAX-safe
    }
}
