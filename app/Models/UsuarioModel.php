<?php 

namespace App\Models; 

use CodeIgniter\Model; 

class UsuarioModel extends Model {
    protected $table = 'usuario';
    protected $primaryKey = 'usuario_id';    
    protected $allowedFields = [ 'nombre','login', 'correo', 'contrasenha', 'permiso']; 
} 