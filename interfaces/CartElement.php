<?php
namespace pistol88\cart\interfaces;

interface CartElement
{
    public function getCartId();

    public function getCartName();

    public function getCartPrice();
    
    public function getCartOptions();
}
