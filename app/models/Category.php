<?php

declare(strict_types=1);

class Category
{
    public function all(): array
    {
        return [
            ['id' => 'calientes', 'name' => 'Cafe caliente', 'icon' => 'cup-soda'],
            ['id' => 'frios', 'name' => 'Cafe frio', 'icon' => 'snowflake'],
            ['id' => 'infusiones', 'name' => 'Te e infusiones', 'icon' => 'leaf'],
            ['id' => 'postres', 'name' => 'Postres', 'icon' => 'cake-slice'],
        ];
    }
}
