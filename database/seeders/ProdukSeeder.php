<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produk;

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $produk = new Produk();
       $produk->insert([
           'nama_barang' => 'Rinso 200ml',
           'harga' => 2000.00,
       ]);

       $produk->insert([
            'nama_barang' => 'Rinso besar',
            'harga' => 30000.00,
       ]);

        $produk->insert([
             'nama_barang' => 'Filma Minyak Goreng',
             'harga' => 25000.00,
        ]);
        $produk->insert([
             'nama_barang' => 'Sabun Mandi',
             'harga' => 5000.00,
        ]);


    }
}
