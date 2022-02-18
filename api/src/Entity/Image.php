<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\UploadImageAction;
use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation\Uploadable;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;



/**
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 */
#[ApiResource(collectionOperations:['get',
'post'=>['method'=>'POST','path'=>'/images',
'controller'=>UploadImageAction::class,'defaults'=>["_api_receive"=>false]]]),
]
#[Uploadable]
class Image
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    #[UploadableField(mapping:"images",fileNameProperty:"url")]
    #[Assert\NotNull()]
    private $file;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups('get-blogpost-with-author')]
    private $url;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return '/images'.$this->url;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }
    public function setFile($file): self
    {
        $this->file = $file;

        return $this;
    }
}
