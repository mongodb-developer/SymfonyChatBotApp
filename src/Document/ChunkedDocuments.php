<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;


#[ODM\Document]
class ChunkedDocuments
{
    #[ODM\Id]
    private $id;


    #[ODM\Field(type: 'string')]
    private $content;
    #[ODM\Field(type: 'string')]
    private $sourceName;
   
    #[ODM\Field(type: 'date')]
    private $createdAt;

    #[ODM\Field(type: 'collection')]
    private array $contentEmbedding = [];



    public function setcontent(string $content): void
    {
        $this->content = $content;
    }

    public function setsourceName (string $sourceName): void
    {
        $this->sourceName = $sourceName;
    }

    public function setcreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getcontent(): ?string
    {
        return $this->content;
    }
    
    public function getsourceName(): ?string
    {
        return $this->sourceName;
    }
    
    public function getcreatedAt(): ?\DateTimeInterface
{
    return $this->createdAt;
}

public function getcontentEmbedding(): array
{
    return $this->contentEmbedding;
}

public function setcontentEmbedding(array $contentEmbedding): self
{
    $this->contentEmbedding = $contentEmbedding;

    return $this;
}

}
