<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\VentaModel;

class Venta extends ResourceController
{    
    use ResponseTrait;
    protected $modelName = 'App\Models\VentaModel';
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

    //Obtener todos los ventas
    public function getIndex()
    {        
        $model = new VentaModel();
        $data = $model->findAll();
        if($data){
            return $this->respond($data, 200);
        }
        else{
            return $this->failNotFound('No Data Found');
        }
    }

    //Obtener venta por id
    public function getPorId($id = null){
        $db = \Config\Database::connect();
        
        $sql = 
        "select v.venta_id, v.fecha, p.nombre, p.precio_venta, vp.cantidad, vp.precio, v.precio_total from venta v
        join venta_producto vp on vp.venta_fk = v.venta_id
        join producto p on p.producto_id = vp.producto_fk
        where v.venta_id = $id";
        
        $data = $db->query($sql)->getResult();
        if($data){
            return $this->respond($data, 200);
        }
        else{
            return $this->failNotFound('No Data Found with id ' . $usuario_id);
        }
    }

    //Crear un venta
    public function postCreate(){
        $model = new VentaModel();
        $data = [
            'precio_total' => $this->request->getVar('precio_total'),
            'fecha' => date('r')
        ];

        $rules = [
            'precio_total' => 'required',
        ];

        if($this->validate($rules)) {
            if($model->insert($data)){
                return $this->respondCreated($model->getInsertId());
            }else{
                return $this->fail("Error en la petición", 400);
            }
        }else{
            return $this->failValidationErrors($this->validator->getErrors());
        }
    }

    //Actualizar un venta
    public function putUpdate(){
        $model = new VentaModel();
        $data = [
            'venta_id' => $this->request->getVar('venta_id'),
            'precio_total' => $this->request->getVar('precio_total')
        ];

        $rules = [
            'venta_id' => 'required',
            'precio_total' => 'required'
        ];

        if($this->validate($rules)) {
            if($model->update($data['venta_id'], $data)){
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

    //Eliminar un venta
    public function deleteDelete($id = null){
        $model = new VentaModel();
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