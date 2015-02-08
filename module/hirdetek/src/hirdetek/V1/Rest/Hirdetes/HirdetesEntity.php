<?php
namespace hirdetek\V1\Rest\Hirdetes;

class HirdetesEntity
{

    public $id;
    public $azonosito;
    public $telepules;
    public $kep;
    public $targy;
    public $szoveg;
    public $ar;
    public $telefon;
    public $feladas;
    public $days_active;
    public $r_rovat_id;
    public $r_rovat_nev;
    public $r_rovat_slug;
    public $p_rovat_id;
    public $p_rovat_nev;
    public $p_rovat_slug;
    public $g_regio_id;
    public $g_regio_nev;
    public $g_regio_slug;
    public $p_regio_id;
    public $p_regio_nev;
    public $p_regio_slug;
    public $image_id;
    public $image_created;
    public $image_nev;
    public $images;

    public function getArrayCopy()
    {
        return array(
            'id'           => $this->id,
            'azonosito'    => $this->azonosito,
            'telepules'    => $this->telepules,
            'kep'          => $this->kep,
            'targy'        => $this->targy,
            'szoveg'       => $this->szoveg,
            'ar'           => $this->ar,
            'telefon'      => $this->telefon,
            'feladas'      => $this->feladas,
            'days_active'  => $this->days_active,
            'r_rovat_id'   => $this->r_rovat_id,
            'r_rovat_nev'  => $this->r_rovat_nev,
            'r_rovat_slug' => $this->r_rovat_slug,
            'p_rovat_id'   => $this->p_rovat_id,
            'p_rovat_nev'  => $this->p_rovat_nev,
            'p_rovat_slug' => $this->p_rovat_slug,
            'g_regio_id'   => $this->g_regio_id,
            'g_regio_nev'  => $this->g_regio_nev,
            'g_regio_slug' => $this->g_regio_slug,
            'p_regio_id'   => $this->p_regio_id,
            'p_regio_nev'  => $this->p_regio_nev,
            'p_regio_slug' => $this->p_regio_slug,
            'image_id'     => $this->image_id,
            'image_created'=> $this->image_created,
            'image_name'   => $this->image_name,
            'images'       => $this->images

        );
    }

    public function exchangeArray(array $array)
    {
        $this->id            = $array['id'];
        $this->azonosito     = $array['azonosito'];
        $this->telepules     = $array['telepules'];
        $this->kep           = $array['kep'];
        $this->targy         = $array['targy'];
        $this->szoveg        = $array['szoveg'];
        $this->ar            = $array['ar'];
        $this->telefon       = $array['telefon'];
        $this->feladas       = $array['feladas'];
        $this->days_active   = $array['days_active'];
        $this->r_rovat_id    = $array['r_rovat_id'];
        $this->r_rovat_nev   = $array['r_rovat_nev'];
        $this->r_rovat_slug  = $array['r_rovat_slug'];
        $this->p_rovat_id    = $array['p_rovat_id'];
        $this->p_rovat_nev   = $array['p_rovat_nev'];
        $this->p_rovat_slug  = $array['p_rovat_slug'];
        $this->g_regio_id    = $array['g_regio_id'];
        $this->g_regio_nev   = $array['g_regio_nev'];
        $this->g_regio_slug  = $array['g_regio_slug'];
        $this->p_regio_id    = $array['p_regio_id'];
        $this->p_regio_nev   = $array['p_regio_nev'];
        $this->p_regio_slug  = $array['p_regio_slug'];
        $this->image_id        = $array['image_id'];
        $this->image_created   = $array['image_created'];
        $this->image_name      = $array['image_name'];
        $this->images        = $array['images'];
    }
}
