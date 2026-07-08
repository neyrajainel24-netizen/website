<?php

declare(strict_types=1);

class MenuController extends BaseController
{
    public function index(): void
    {
        $selectedCategory = $_GET['category'] ?? 'calientes';
        $productModel = new Product();
        $categoryModel = new Category();

        $this->render('menu/index', [
            'pageTitle' => 'Menu',
            'categories' => $categoryModel->all(),
            'selectedCategory' => $selectedCategory,
            'products' => $productModel->byCategory($selectedCategory),
        ]);
    }
}

