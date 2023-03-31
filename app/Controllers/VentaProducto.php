<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\VentaProductoModel;

class VentaProducto extends ResourceController
{    
    use ResponseTrait;
    protected $modelName = 'App\Models\VentaProductoModel';
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

    //Obtener todos los venta_productos
    public function getIndex()
    {        
        $model = new VentaProductoModel();
        $data = $model->findAll();
        if($data){
            return $this->respond($data, 200);
        }
        else{
            return $this->failNotFound('No Data Found');
        }
    }

    //Obtener venta_producto por id
    public function getPorId($id = null){
        $model = new VentaProductoModel();
        $data = $model->getWhere(['venta_producto_id' => $id])->getResult();
        if($data){
            return $this->respond($data, 200);
        }
        else{
            return $this->failNotFound('No Data Found with id ' . $id);
        }
    }

    //Crear un venta_producto
    public function postCreate(){
        $model = new VentaProductoModel();
        $data = [
            'venta_fk' => $this->request->getVar('venta_fk'),
            'producto_fk' => $this->request->getVar('producto_fk'),
            'cantidad' => $this->request->getVar('cantidad'),
            'precio' => $this->request->getVar('precio'),
        ];

        $rules = [
            'venta_fk' => 'required',
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

    //Actualizar un venta_producto
    public function putUpdate(){
        $model = new VentaProductoModel();
        $data = [
            'venta_producto_id' => $this->request->getVar('venta_producto_id'),
            'venta_fk' => $this->request->getVar('venta_fk'),
            'producto_fk' => $this->request->getVar('producto_fk'),
            'cantidad' => $this->request->getVar('cantidad'),
            'precio' => $this->request->getVar('precio')
        ];

        $rules = [
            'venta_producto_id' => 'required',
            'venta_fk' => 'required',
            'producto_fk' => 'required',
            'cantidad' => 'required',
            'precio' => 'required'
        ];

        if($this->validate($rules)) {
            if($model->update($data['venta_producto_id'], $data)){
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

    //Eliminar un venta_producto
    public function deleteDelete($id = null){
        $model = new VentaProductoModel();
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