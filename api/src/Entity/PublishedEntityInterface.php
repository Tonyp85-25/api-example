<?php

namespace App\Entity;

interface PublishedEntityInterface
{
    public function setPublished(\DateTimeInterface $published): PublishedEntityInterface;
}