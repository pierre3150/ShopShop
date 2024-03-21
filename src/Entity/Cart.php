<?php

declare (strict_types = 1);

namespace MyApp\Entity;

use MyApp\Entity\User;

class Cart
{
    private ?int $id;
    private string $creationdate;
    private string $status;
    private User $user;

    public function __construct(?int $id, string $creationdate, string $status, User $user)
    {
        $this->id = $id;
        $this->creationdate = $creationdate;
        $this->status = $status;
        $this->user = $user;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getCreationDate(): string
    {
        return $this->creationdate;
    }

    public function setCreationDate(?string $creationdate): void
    {
        $this->creationdate = $creationdate;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    public function getUser(): User
    {
        return $this->user;
    }
    
    public function setUser(User $user): void
    {
        $this->user = $user;
    }
}
