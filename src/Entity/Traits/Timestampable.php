<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait Timestampable
{
    /**
     * @ORM\Column(type="datetime_immutable",options={"default":"CURRENT_TIMESTAMP"} )
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable",options={"default":"CURRENT_TIMESTAMP"})
     */
    private $updateAt;

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeImmutable
    {
        return $this->updateAt;
    }

    public function setUpdateAt(\DateTimeImmutable $updateAt): self
    {
        $this->updateAt = $updateAt;

        return $this;
    }
    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateTimestamps()
    {
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new \DateTimeImmutable);
        }

        $this->setUpdateAt(new \DateTimeImmutable);
    }
}
