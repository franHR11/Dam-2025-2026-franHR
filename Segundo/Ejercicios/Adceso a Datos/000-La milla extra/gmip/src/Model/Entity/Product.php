<?php

declare(strict_types=1);

namespace GMIP\Model\Entity;

/**
 * Entidad Producto (base para mapeo ORM más adelante)
 */
class Product
{
    public ?int $id = null;
    public string $name;
    public string $sku;
    public float $price;
    public int $stock;
    public ?int $providerId = null;
    public string $createdAt;

    public function __construct(string $name, string $sku, float $price, int $stock, ?int $providerId = null)
    {
        $this->name = $name;
        $this->sku = $sku;
        $this->price = $price;
        $this->stock = $stock;
        $this->providerId = $providerId;
        $this->createdAt = date('Y-m-d H:i:s');
    }

    public static function fromArray(array $data): self
    {
        $p = new self(
            (string)$data['name'],
            (string)$data['sku'],
            (float)$data['price'],
            (int)$data['stock'],
            isset($data['providerId']) ? (int)$data['providerId'] : null
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
            'sku' => $this->sku,
            'price' => $this->price,
            'stock' => $this->stock,
            'providerId' => $this->providerId,
            'createdAt' => $this->createdAt,
        ];
    }
}