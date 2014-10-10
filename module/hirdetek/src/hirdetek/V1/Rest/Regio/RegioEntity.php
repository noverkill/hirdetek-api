<?php
namespace hirdetek\V1\Rest\Regio;

class RegioEntity
{
	public $id;
    public $nev;
    public $slug;
    public $order;
    public $parent;

    public function getArrayCopy()
    {
        return array(
            'id'     => $this->id,
            'nev'    => $this->nev,
            'slug'   => $this->slug,
            'order'  => $this->order,
            'parent' => $this->parent,
        );
    }

    public function exchangeArray(array $array)
    {
        $this->id     = $array['id'];
        $this->nev    = $array['nev'];
        $this->slug   = $array['slug'];
        $this->order  = $array['order'];
        $this->parent = $array['parent'];
    }
}