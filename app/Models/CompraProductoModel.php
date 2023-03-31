<?php 

namespace App\Models; 

use CodeIgniter\Model; 

class CompraProductoModel extends Model {
    protected $table = 'compra_producto';
    protected $primaryKey = 'compra_producto_id';    
    protected $allowedFields = ['compra_fk', 'producto_fk', 'cantidad', 'precio']; 
} 