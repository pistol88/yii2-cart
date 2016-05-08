<?php
namespace pistol88\cart\interfaces;

interface CartElementService
{
    public function getCartId();

    public function getCartElementModel();

    public function getPrice();

    public function getOptions();
    
    public function getCount();
    
    public function getCost();
}
