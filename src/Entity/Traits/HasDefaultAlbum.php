<?php

namespace App\Entity\Traits;

use App\Entity\Album;
use Doctrine\Common\Collections\Collection;

trait HasDefaultAlbum
{
    abstract public function getAlbums(): Collection;

    /**
     * Get default album
     *
     * @return Album|false
     */
    public function getDefaultAlbum(): Album|false
    {
        return $this->getAlbums()->filter(function ($album) {
            /** @var Album $album */
            return $album->getType() == Album::GROUP_DEFAULT_TYPE || $album->getType() == Album::USER_DEFAULT_TYPE;
        })->first();
    }
}