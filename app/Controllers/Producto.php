<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\ProductoModel;

class Producto extends ResourceController
{    
    use ResponseTrait;
    protected $modelName = 'App\Models\ProductoModel';
    protected $format    = 'json';
    
    public function __construct()
    {
        Header('Access-Control-Allow-Origin: *'); 
        Header('Access-Control-Allow-Headers: *'); 
        Header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE'); 
    }

    //Options
    public function optionsIndex(){
        return $this->respond(true);
    }

    //Obtener todos los productos
    public function getIndex()
    {        
        $model = new ProductoModel();
        $data = $model->orderBy('nombre', 'asc')->getWhere(['estatus' => 1])->getResult();
        if($data){
            return $this->respond($data, 200);
        }
        else{
            return $this->failNotFound('No Data Found');
        }
    }

    //Obtener producto por id
    public function getPorId($id = null){
        $model = new ProductoModel();
        $data = $model->getWhere(['producto_id' => $id])->getResult();
        if($data){
            return $this->respond($data, 200);
        }
        else{
            return $this->failNotFound('No Data Found with id ' . $id);
        }
    }

    //Obtener producto por usuario
    public function getPorUsuario($usuario_id = null){
        $model = new ProductoModel();
        $data = $model->getWhere(['usuario_fk' => $usuario_id, 'estatus' => 1])->getResult();
        if($data){
            return $this->respond($data, 200);
        }
        else{
            return $this->failNotFound('No Data Found with id ' . $usuario_id);
        }
    }

    //Crear un producto
    public function postCreate(){
        $model = new ProductoModel();
        $data = [
            'cantidad' => $this->request->getVar('cantidad'),
            //Mandar el nombre a lowercase desde el Frontend
            'nombre' => $this->request->getVar('nombre'),
            'codigo_barras' => $this->request->getVar('codigo_barras'),
            'precio_compra' => $this->request->getVar('precio_compra'),
            'precio_venta' => $this->request->getVar('precio_venta'),
            'usuario_fk' => $this->request->getVar('usuario_fk')
        ];

        $rules = [
            'nombre' => 'required|is_unique[producto.nombre]',
            'codigo_barras' => 'is_unique[producto.codigo_barras]',
            'precio_compra' => 'required',
            'precio_venta' => 'required',
            'usuario_fk' => 'required'
        ];

        if($this->validate($rules)) {
            if($model->insert($data)){
                return $this->respondCreated($data);
            }else{
                return $this->fail("Error en la petición", 400);
            }
        }else{
            return $this->failValidationErrors($this->validator->getErrors());
        }
    }

    //Actualizar un producto
    public function putUpdate(){
        $model = new ProductoModel();
        $data = [
            'producto_id' => $this->request->getVar('producto_id'),
            'cantidad' => $this->request->getVar('cantidad'),
            //Mandar el nombre a lowercase desde el Frontend
            'nombre' => $this->request->getVar('nombre'),
            'codigo_barras' => $this->request->getVar('codigo_barras'),
            'precio_compra' => $this->request->getVar('precio_compra'),
            'precio_venta' => $this->request->getVar('precio_venta'),
            'usuario_fk' => $this->request->getVar('usuario_fk')
        ];

        $rules = [
            'producto_id' => 'required',
            'nombre' => 'required|is_unique[producto.nombre,producto_id,' . $data['producto_id'] . ']',
            'codigo_barras' => 'is_unique[producto.codigo_barras,producto_id,' . $data['producto_id'] . ']',
            'precio_compra' => 'required',
            'precio_venta' => 'required',
            'usuario_fk' => 'required'
        ];

        //Manejar excepciones de la base de datos
        if($this->validate($rules)) {
            if($model->update($data['producto_id'], $data)){
                return $this->respondCreated($data);
            }
            else{
                return $this->fail("Error en la petición", 400);
            }
        }
        else{
            return $this->failValidationErrors($this->validator->getErrors());
        }
    }

    //Eliminar un producto
    public function deleteDelete($id = null){
        $model = new ProductoModel();
        if($id){
            if($model->update($id, ['estatus' => 0])){
                return $this->respondDeleted($id);
            }
            else{
                return $this->failForbidden();
            }
        }
        else{
            return $this->failNotFound("No encontrado");
        }
    }
}