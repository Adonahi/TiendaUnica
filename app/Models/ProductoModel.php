<?php 

namespace App\Models; 

use CodeIgniter\Model; 

class ProductoModel extends Model {
    protected $table = 'producto';
    protected $primaryKey = 'producto_id';    
    protected $allowedFields = [ 'existencia','nombre','codigo_barras', 'precio_compra', 'precio_venta', 'usuario_fk', 'estatus']; 
} 