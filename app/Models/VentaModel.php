<?php 

namespace App\Models; 

use CodeIgniter\Model; 

class VentaModel extends Model {
    protected $table = 'venta';
    protected $primaryKey = 'venta_id';    
    protected $allowedFields = [ 'precio_total']; 
} 