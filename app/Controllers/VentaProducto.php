<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\VentaProductoModel;
use App\Models\ProductoModel;

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
        $mProducto = new ProductoModel();
        $data = [
            'venta_fk' => $this->request->getVar('venta_fk'),
            'producto_fk' => $this->request->getVar('producto_fk'),
            'existencia' => $this->request->getVar('existencia'),
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
                if($mProducto->update($data['producto_fk'], ['existencia' => $data['existencia'] - $data['cantidad']])){
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

    //Obtener productos vendidos por usuario
    public function getPorUsuario($usuario_id = null){
        
        $db = \Config\Database::connect();
        
        $sql = 
        "select v.venta_id, v.fecha, p.nombre, p.precio_venta, p.precio_compra, vp.cantidad, vp.precio from venta_producto vp
        join venta v on v.venta_id = vp.venta_fk
        join producto p on p.producto_id = vp.producto_fk
        where p.usuario_fk = $usuario_id
        order by v.fecha desc";
        
        $data = $db->query($sql)->getResult();
        if($data){
            return $this->respond($data, 200);
        }
        else{
            return $this->failNotFound('No Data Found with id ' . $usuario_id);
        }
    }
    
    public function getPorUsuarioPorProducto($usuario_id = null){
        
        $db = \Config\Database::connect();
        
        $sql = 
        "select p.nombre, sum(vp.precio) from venta_producto vp
        join producto p on p.producto_id = vp.producto_fk
        where p.usuario_fk = $usuario_id
        group by p.nombre;";
        
        $data = $db->query($sql)->getResult();
        if($data){
            return $this->respond($data, 200);
        }
        else{
            return $this->failNotFound('No Data Found with id ' . $usuario_id);
        }
    }
}