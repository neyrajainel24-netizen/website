<?php

declare(strict_types=1);

class HomeController extends BaseController
{
    public function index(): void
    {
        $productModel = new Product();

        $this->render('home/index', [
            'pageTitle' => 'Inicio',
            'featuredProducts' => $productModel->featured(),
        ]);
    }
}
