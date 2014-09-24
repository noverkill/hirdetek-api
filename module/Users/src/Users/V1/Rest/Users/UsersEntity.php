<?php
namespace Users\V1\Rest\Users;

class UsersEntity
{
	public $id;
    public $bejnev;
    public $email;
 
    public function getArrayCopy()
    {
        return array(
            'id'     => $this->id,
            'bejnev' => $this->bejnev,
            'email'  => $this->email,
        );
    }
 
    public function exchangeArray(array $array)
    {
        $this->id     = $array['id'];
        $this->bejnev = $array['bejnev'];
        $this->email  = $array['email'];
    }
}
