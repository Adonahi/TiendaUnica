<?php 

namespace App\Models; 

use CodeIgniter\Model; 

class VentaProductoModel extends Model {
    protected $table = 'venta_producto';
    protected $primaryKey = 'venta_producto_id';    
    protected $allowedFields = ['venta_fk', 'producto_fk', 'cantidad', 'precio']; 
} 