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
            'targy'  => $this->targy,
            'szoveg' => $this->szoveg,
            'ar'     => $this->ar,
        );
    }

    public function exchangeArray(array $array)
    {
        $this->id     = $array['id'];
        $this->kep    = $array['kep'];
        $this->targy  = $array['targy'];
        $this->szoveg = $array['szoveg'];
        $this->ar     = $array['ar'];
    }
}
