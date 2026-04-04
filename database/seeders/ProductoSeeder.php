<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            ['nombre' => 'Coca-Cola 600ml', 'precio' => 18.50, 'stock' => 50, 'stock_minimo' => 10, 'codigo_barras' => '7501234567891', 'categoria' => 'Bebidas'],
            ['nombre' => 'Pepsi 600ml', 'precio' => 17.00, 'stock' => 45, 'stock_minimo' => 10, 'codigo_barras' => '7501234567892', 'categoria' => 'Bebidas'],
            ['nombre' => 'Agua Bonafont 1L', 'precio' => 12.00, 'stock' => 60, 'stock_minimo' => 15, 'codigo_barras' => '7501234567893', 'categoria' => 'Bebidas'],
            ['nombre' => 'Boing Mango 500ml', 'precio' => 15.00, 'stock' => 30, 'stock_minimo' => 8, 'codigo_barras' => '7501234567894', 'categoria' => 'Bebidas'],
            ['nombre' => 'Café Late 350ml', 'precio' => 28.00, 'stock' => 20, 'stock_minimo' => 5, 'codigo_barras' => '7501234567895', 'categoria' => 'Bebidas'],
            
            ['nombre' => 'Doritos Nacho 200g', 'precio' => 32.00, 'stock' => 25, 'stock_minimo' => 8, 'codigo_barras' => '7509876543211', 'categoria' => 'Snacks'],
            ['nombre' => 'Cheetos 150g', 'precio' => 28.00, 'stock' => 22, 'stock_minimo' => 8, 'codigo_barras' => '7509876543212', 'categoria' => 'Snacks'],
            ['nombre' => 'Cheetos Torcidinhos 120g', 'precio' => 24.00, 'stock' => 18, 'stock_minimo' => 6, 'codigo_barras' => '7509876543213', 'categoria' => 'Snacks'],
            ['nombre' => 'Paketaxo 200g', 'precio' => 26.00, 'stock' => 15, 'stock_minimo' => 5, 'codigo_barras' => '7509876543214', 'categoria' => 'Snacks'],
            ['nombre' => 'Runners Salados 180g', 'precio' => 22.00, 'stock' => 20, 'stock_minimo' => 6, 'codigo_barras' => '7509876543215', 'categoria' => 'Snacks'],
            
            ['nombre' => 'Galletas Oreo 144g', 'precio' => 35.00, 'stock' => 30, 'stock_minimo' => 10, 'codigo_barras' => '7501112223331', 'categoria' => 'Dulces'],
            ['nombre' => 'Chocolate Hershey 100g', 'precio' => 38.00, 'stock' => 25, 'stock_minimo' => 8, 'codigo_barras' => '7501112223332', 'categoria' => 'Dulces'],
            ['nombre' => 'Chicle Trident 12p', 'precio' => 18.00, 'stock' => 40, 'stock_minimo' => 15, 'codigo_barras' => '7501112223333', 'categoria' => 'Dulces'],
            ['nombre' => 'Pulparindo 50g', 'precio' => 12.00, 'stock' => 50, 'stock_minimo' => 15, 'codigo_barras' => '7501112223334', 'categoria' => 'Dulces'],
            ['nombre' => 'Mazapán 25g', 'precio' => 8.00, 'stock' => 60, 'stock_minimo' => 20, 'codigo_barras' => '7501112223335', 'categoria' => 'Dulces'],
            
            ['nombre' => 'Cigarros Marlboro 20', 'precio' => 85.00, 'stock' => 30, 'stock_minimo' => 10, 'codigo_barras' => '7502223334441', 'categoria' => 'Tabaco'],
            ['nombre' => 'Cigarros Camel 20', 'precio' => 82.00, 'stock' => 25, 'stock_minimo' => 10, 'codigo_barras' => '7502223334442', 'categoria' => 'Tabaco'],
            
            ['nombre' => 'Chamarra Impermeable', 'precio' => 250.00, 'stock' => 5, 'stock_minimo' => 3, 'codigo_barras' => '7503334445551', 'categoria' => 'Varios'],
            ['nombre' => 'Paraguas', 'precio' => 85.00, 'stock' => 10, 'stock_minimo' => 4, 'codigo_barras' => '7503334445552', 'categoria' => 'Varios'],
            ['nombre' => 'Bolsas Kilo', 'precio' => 5.00, 'stock' => 100, 'stock_minimo' => 30, 'codigo_barras' => '7503334445553', 'categoria' => 'Varios'],
        ];

        foreach ($productos as $producto) {
            Producto::create(array_merge($producto, ['activo' => true]));
        }
    }
}
