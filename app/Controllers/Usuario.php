<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\UsuarioModel;

class Usuario extends ResourceController
{    
    use ResponseTrait;
    protected $modelName = 'App\Models\UsuarioModel';
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

    //Obtener todos los usuarios
    public function getIndex()
    {        
        $model = new UsuarioModel();
        $data = $model->findAll();
        if($data){
            return $this->respond($data, 200);
        }
        else{
            return $this->failNotFound('No Data Found');
        }
    }

    //Obtener usuario por id
    public function getPorId($id = null){
        $model = new UsuarioModel();
        $data = $model->getWhere(['usuario_id' => $id])->getResult();
        if($data){
            return $this->respond($data, 200);
        }
        else{
            return $this->failNotFound('No Data Found with id ' . $id);
        }
    }

    //Crear un usuario
    public function postCreate(){
        $model = new UsuarioModel();
        $data = [
            'nombre' => $this->request->getVar('nombre'),
            'login' => $this->request->getVar('login'),
            'correo' => $this->request->getVar('correo'),
            'contrasenha' => $this->request->getVar('contrasenha'),
            'permiso' => $this->request->getVar('permiso')
        ];

        $rules = [
            'nombre' => 'required',
            'login' => 'required|is_unique[usuario.login]',
            'correo' => 'required|is_unique[usuario.correo]',
            'contrasenha' => 'required',
            'permiso' => 'required'
        ];

        $data['contrasenha'] = password_hash($data['contrasenha'], PASSWORD_DEFAULT);

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

    //Actualizar un usuario
    public function putUpdate(){
        $model = new UsuarioModel();
        $data = [
            'usuario_id' => $this->request->getVar('usuario_id'),
            'nombre' => $this->request->getVar('nombre'),
            'login' => $this->request->getVar('login'),
            'correo' => $this->request->getVar('correo'),
            'contrasenha' => $this->request->getVar('contrasenha'),
            'permiso' => $this->request->getVar('permiso')
        ];

        $rules = [
            'usuario_id' => 'required',
            'nombre' => 'required',
            'login' => 'required',
            'correo' => 'required',
            'contrasenha' => 'required',
            'permiso' => 'required'
        ];

        $data['contrasenha'] = password_hash($data['contrasenha'], PASSWORD_DEFAULT);

        //Manejar excepciones de la base de datos
        if($this->validate($rules)) {
            if($model->update($data['usuario_id'], $data)){
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

    //Eliminar un usuario
    public function deleteDelete($id = null){
        $model = new UsuarioModel();
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