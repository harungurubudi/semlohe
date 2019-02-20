<?php
namespace App\Semlohe\Views;

interface ViewInterface
{
    public function __construct($twig);
    public function render($data);
}
