<?php

declare(strict_types=1);

namespace GMIP\Model\Entity;

/**
 * Entidad Proveedor
 */
class Provider
{
    public ?int $id = null;
    public string $name;
    public string $email;
    public string $phone;
    public string $createdAt;

    public function __construct(string $name, string $email, string $phone)
    {
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->createdAt = date('Y-m-d H:i:s');
    }

    public static function fromArray(array $data): self
    {
        $p = new self(
            (string)$data['name'],
            (string)$data['email'],
            (string)$data['phone']
        );
        if (isset($data['id'])) $p->id = (int)$data['id'];
        if (isset($data['createdAt'])) $p->createdAt = (string)$data['createdAt'];
        return $p;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'createdAt' => $this->createdAt,
        ];
    }
}