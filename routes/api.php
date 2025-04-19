<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// moved the 'auth.php' to 'api.php' which removes the CSRF token mismatch issue
// as we are using the endpoint as api endpoints 
require __DIR__.'/auth.php';


