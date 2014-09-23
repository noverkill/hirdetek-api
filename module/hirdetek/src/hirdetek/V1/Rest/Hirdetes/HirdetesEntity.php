<?php
namespace hirdetek\V1\Rest\Hirdetes;

class HirdetesEntity
{
	public $id;
    public $kep;
    public $szoveg;
 
    public function getArrayCopy()
    {
        return array(
            'id'     => $this->id,
            'kep'    => $this->kep,
            'szoveg' => $this->szoveg,
        );
    }
 
    public function exchangeArray(array $array)
    {
        $this->id     = $array['id'];
        $this->kep    = $array['kep'];
        $this->szoveg = $array['szoveg'];
    }
}
