<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\UsuarioModel;
use Config\Services;
use \Firebase\JWT\JWT;
use \Datetime;

class Usuario extends ResourceController
{    
    use ResponseTrait;
    protected $modelName = 'App\Models\UsuarioModel';
    protected $format    = 'json';
    private $alg = 'HS256';
    
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
            'correo' => $this->request->getVar('correo'),
            'contrasenha' => $this->request->getVar('contrasenha'),
            'permiso' => $this->request->getVar('permiso')
        ];

        $rules = [
            'nombre' => 'required',
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
            'correo' => $this->request->getVar('correo'),
            'contrasenha' => $this->request->getVar('contrasenha'),
            'permiso' => $this->request->getVar('permiso')
        ];

        $rules = [
            'usuario_id' => 'required',
            'nombre' => 'required',
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

    public function postLogout()
    {
        $db = \Config\Database::connect();
        $model = new UsuarioModel();
        $data = [
            'usuario_id' => $this->request->getVar('usuario_id')
        ];

        $rules = [
            'usuario_id' => 'required'
        ];

        if ($this->validate($rules)) {
            $sql = 'update usuario set login=\'\' where usuario_id= ' . $data['usuario_id'] . ';';
            $data = $db->query($sql);
            return $this->respond($data);
        } else {
            return $this->failValidationErrors($this->validator->getErrors());
        }
    }

    public function postLogin()
    {
        $db = \Config\Database::connect();
        $model = new UsuarioModel();
        $data = [
            'password' => $this->request->getVar('password'),
            'correo' => $this->request->getVar('correo')
        ];

        $rules = [
            'password' => 'required',
            'correo' => 'required'
        ];
        if ($this->validate($rules)) {
            $sql = 'select * from usuario where correo = \'' . $data['correo'] . '\';';
            $query = $db->query($sql);

            if (count($query->getResult()) > 0) {
                $usuario_id = $query->getResult()[0]->usuario_id;
                $row = $query->getResult()[0];

                if (password_verify($data['password'], $row->contrasenha)) {
                    $sql = 'select login from usuario where usuario_id= ' . $usuario_id . ';';
                    $tmstmpLogin = $db->query($sql);
                    $tmstmpAntiguo = $tmstmpLogin->getResult()[0]->login;
                    $horaLimite = date('Y-m-d H:i:s', strtotime('+10 minutes', strtotime($tmstmpAntiguo)));
                    if (date('Y-m-d H:i:s') <= $horaLimite) {
                        return $this->failValidationErrors("Este usuario tiene una sesión abierta en otro dispositivo");
                    } else {
                        $issuedat_claim = time();
                        $expire_claim = $issuedat_claim + 3600;
                        $token = array(
                            'iat' => $issuedat_claim,
                            'exp' => $expire_claim,
                            'data' => array(
                                'login' => $row->login,
                                'correo' => $row->correo,
                                'nombre' => $row->nombre,
                                'permiso' => $row->permiso,
                                'usuario_id' => $row->usuario_id
                            )
                        );

                        $key = Services::getKey();
                        $jwtValue = JWT::encode($token, $key, $this->alg);
                        $tmstmp = new DateTime();
                        $sql = 'update usuario set login=\'' . $tmstmp->format('Y-m-d H:i:s') . '\' where usuario_id= ' . $usuario_id . ';';
                        $db->query($sql);
                        $query = $db->query($sql);

                        return json_encode(
                            array(
                                "status" => "success",
                                "token" => $jwtValue,
                            )
                        );
                    }
                } else {
                    return $this->failValidationErrors("La contraseña es incorrecta");
                }
            } else {
                return $this->failValidationErrors("El correo introducido no está asociado con alguna cuenta");
            }
        } else {
            return $this->failValidationErrors($this->validator->getErrors());
        }
    }
}