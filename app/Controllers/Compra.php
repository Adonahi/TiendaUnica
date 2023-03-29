<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\CompraModel;

class Compra extends ResourceController
{    
    use ResponseTrait;
    protected $modelName = 'App\Models\CompraModel';
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

    //Obtener todos los compras
    public function getIndex()
    {        
        $model = new CompraModel();
        $data = $model->findAll();
        if($data){
            return $this->respond($data, 200);
        }
        else{
            return $this->failNotFound('No Data Found');
        }
    }

    //Obtener compra por id
    public function getPorId($id = null){
        $model = new CompraModel();
        $data = $model->getWhere(['compra_id' => $id])->getResult();
        if($data){
            return $this->respond($data, 200);
        }
        else{
            return $this->failNotFound('No Data Found with id ' . $id);
        }
    }

    //Crear un compra
    public function postCreate(){
        $model = new CompraModel();
        $data = [
            'precio_total' => $this->request->getVar('precio_total')
        ];

        $rules = [
            'precio_total' => 'required',
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

    //Actualizar un compra
    public function putUpdate(){
        $model = new CompraModel();
        $data = [
            'compra_id' => $this->request->getVar('compra_id'),
            'precio_total' => $this->request->getVar('precio_total')
        ];

        $rules = [
            'compra_id' => 'required',
            'precio_total' => 'required'
        ];

        if($this->validate($rules)) {
            if($model->update($data['compra_id'], $data)){
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

    //Eliminar un compra
    public function deleteDelete($id = null){
        $model = new CompraModel();
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