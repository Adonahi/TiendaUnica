<?php 

namespace App\Models; 

use CodeIgniter\Model; 

class CompraModel extends Model {
    protected $table = 'compra';
    protected $primaryKey = 'compra_id';    
    protected $allowedFields = [ 'precio_total']; 
} 