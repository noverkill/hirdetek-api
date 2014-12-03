<?php
namespace Users\V1\Rest\Users;

class UsersEntity
{
	public $id;
    public $nev;
    //public $bejnev;
    //public $email;
    public $weblap;
    public $telefon;
    public $varos;
    public $regio;
    public $altkategoria;
 
    public function getArrayCopy()
    {
        return array(
            'id'           => $this->id,
            'nev'          => $this->nev,
            //'bejnev'       => $this->bejnev,
            //'email'        => $this->email,
            'weblap'       => $this->weblap,
            'telefon'      => $this->telefon,
            'varos'        => $this->varos,
            'regio'        => $this->regio,
            'altkategoria' => $this->altkategoria
        );
    }
 
    public function exchangeArray(array $array)
    {
        $this->id           = $array['id']; 
        $this->nev          = $array['nev']; 
        //$this->bejnev       = $array['bejnev']; 
        //$this->email        = $array['email']; 
        $this->weblap       = $array['weblap']; 
        $this->telefon      = $array['telefon']; 
        $this->varos        = $array['varos']; 
        $this->regio        = $array['regio']; 
        $this->altkategoria = $array['altkategoria'];
    }
}
