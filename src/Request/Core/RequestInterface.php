<?php


namespace App\Request\Core;

use Symfony\Component\HttpFoundation\Request;

interface RequestInterface
{
    public function __construct(Request $request);
}