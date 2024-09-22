<?php

namespace App\Interfaces;

interface PostcodeRepositoryInterface 
{
    public function findPostcodeByCode(string $postcode);
}