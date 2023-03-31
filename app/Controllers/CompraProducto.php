<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\CompraProductoModel;

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
            return $this->failNotFound('No Data Found');
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
            return $this->failNotFound('No Data Found with id ' . $id);
        }
    }

    //Crear un compra_producto
    public function postCreate(){
        $model = new CompraProductoModel();
        $data = [
            'compra_fk' => $this->request->getVar('compra_fk'),
            'producto_fk' => $this->request->getVar('producto_fk'),
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
                return $this->respondCreated($data);
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
}