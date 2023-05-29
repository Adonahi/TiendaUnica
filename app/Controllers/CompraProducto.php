<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\CompraProductoModel;
use App\Models\ProductoModel;

class CompraProducto extends ResourceController
{    
    use ResponseTrait;
    protected $modelName = 'App\Models\CompraProductoModel';
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

    //Obtener todos los compra_productos
    public function getIndex()
    {        
        $model = new CompraProductoModel();
        $data = $model->findAll();
        if($data){
            return $this->respond($data, 200);
        }
        else{
            return $this->respond([]);
        }
    }

    //Obtener compra_producto por id
    public function getPorId($id = null){
        $model = new CompraProductoModel();
        $data = $model->getWhere(['compra_producto_id' => $id])->getResult();
        if($data){
            return $this->respond($data, 200);
        }
        else{
            return $this->respond([]);
        }
    }

    //Crear un compra_producto
    public function postCreate(){
        $model = new CompraProductoModel();
        $mProducto = new ProductoModel();
        $data = [
            'compra_fk' => $this->request->getVar('compra_fk'),
            'producto_fk' => $this->request->getVar('producto_fk'),
            'existencia' => $this->request->getVar('existencia'),
            'cantidad' => $this->request->getVar('cantidad'),
            'precio' => $this->request->getVar('precio'),
        ];

        $rules = [
            'compra_fk' => 'required',
            'producto_fk' => 'required',
            'cantidad' => 'required',
            'precio' => 'required'
        ];

        if($this->validate($rules)) {
            if($model->insert($data)){
                if($mProducto->update($data['producto_fk'], ['existencia' => $data['existencia'] + $data['cantidad']])){
                    return $this->respondCreated($data);
                }
                else{
                    return $this->fail("Error en la petición", 400);    
                }
            }else{
                return $this->fail("Error en la petición", 400);
            }
        }else{
            return $this->failValidationErrors($this->validator->getErrors());
        }
    }

    //Actualizar un compra_producto
    public function putUpdate(){
        $model = new CompraProductoModel();
        $data = [
            'compra_producto_id' => $this->request->getVar('compra_producto_id'),
            'compra_fk' => $this->request->getVar('compra_fk'),
            'producto_fk' => $this->request->getVar('producto_fk'),
            'cantidad' => $this->request->getVar('cantidad'),
            'precio' => $this->request->getVar('precio')
        ];

        $rules = [
            'compra_producto_id' => 'required',
            'compra_fk' => 'required',
            'producto_fk' => 'required',
            'cantidad' => 'required',
            'precio' => 'required'
        ];

        if($this->validate($rules)) {
            if($model->update($data['compra_producto_id'], $data)){
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

    //Eliminar un compra_producto
    public function deleteDelete($id = null){
        $model = new CompraProductoModel();
        if($id){
            if($model->delete($id)){
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

    //Obtener productos vendidos por usuario
    public function getPorUsuario($usuario_id = null){
        
        $db = \Config\Database::connect();
        
        $sql = 
        "select c.compra_id, c.fecha, p.nombre, p.precio_compra, cp.cantidad, cp.precio from compra_producto cp
        join compra c on c.compra_id = cp.compra_fk
        join producto p on p.producto_id = cp.producto_fk
        where p.usuario_fk = $usuario_id
        order by c.fecha desc";
        
        $data = $db->query($sql)->getResult();
        if($data){
            return $this->respond($data, 200);
        }
        else{
            return $this->respond([]);
        }
    }
}