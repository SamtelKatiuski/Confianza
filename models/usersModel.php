<?php

class usersModel extends Model 
{
    public function __construct()
    {
        parent::__construct();        
    }

    //Verificar si usuario existe
    public function exist($username)
    {
        $result = $this->_db->prepare("SELECT * FROM users WHERE username = :username");
        $result->bindValue(":username", $username);
        $result->execute();

        $res = $result->fetchAll();

        if(count($res))
        {
            return true;
        }

        return false;
    }

    //Verificar si usuario existe por id
    public function existId($username, $id)
    {
		    $result = $this->_db->prepare("SELECT * FROM users WHERE username = :username AND id <> :id");
        $result->bindValue(":username", $username);
        $result->bindValue(":id", $id);
        $result->execute();

        $res = $result->fetchAll();

        if(count($res))
        {
            return true;
        }

        return false;
    }

    //Obtener información de usuario
    public function getData($username)
    {
        $result = $this->_db->prepare("SELECT A.* ,  B.rol_name as role 
                                       FROM users A
                                       INNER JOIN user_roles B
                                       ON B.id = A.role_type
                                       WHERE username = :username");
        $result->bindValue(":username", $username);
        $result->execute();

        return $result->fetchAll();
    }

    //Cargar listado de usuarios
    public function loadUsers()
    {
        $resul = $this->_db->prepare("SELECT 
                                      A.id,
                                      A.nombre,
                                      A.username,
                                      B.rol_name,
                                      B.rol_desc,
                                      A.status                                      
                                      FROM users A
                                      INNER JOIN user_roles B
                                      ON A.role_type = B.id
                                      WHERE A.username <> :username");
        $resul->bindValue(":username", Session::get("Mundial_authenticate_username"));
        $resul->execute();

        return $resul->fetchAll();
    }

    //Obtener lista de roles de usuario
    public function getRoles()
    {
        $resul = $this->_db->prepare("SELECT 
                                      id,
                                      rol_name
                                      FROM user_roles 
                                      ORDER BY id DESC");
        $resul->execute();

        return $resul->fetchAll();

    }

    //Obtener descripción de rol de usuario
    public function getRoleDescription($id)
    {
        $resul = $this->_db->prepare("SELECT 
                                      rol_desc
                                      FROM user_roles 
                                      WHERE id = :id");
        $resul->bindValue(":id", $id);
        $resul->execute();

        return $resul->fetchAll();

    }

    //Guardar datos de usuario
    public function insert($data)
    {
		  $resul = $this->_db->prepare("INSERT INTO users VALUES (null, :full_name, :username, :password, :hash_key, NOW(), :role, NOW(), 1, :correo ,null)");

      $resul->bindValue(":full_name", $data['nombre']);
      $resul->bindValue(":username", $data['username']);
      $resul->bindValue(":password", $data['password']);
      $resul->bindValue(":hash_key", $data['hash_key']);
      $resul->bindValue(":role", $data['role']);
      $resul->bindValue(":correo", $data['correo']);
      return $resul->execute();
    }

    //Actualizar datos de usuario
    public function update($data)
    {

        $resul = $this->_db->prepare("UPDATE users SET nombre = :full_name, username = :username, role_type = :role, correo = :correo , status = :status WHERE id = :id");

        $resul->bindValue(":id", $data['id']);
        $resul->bindValue(":full_name", $data['nombre']);
        $resul->bindValue(":username", $data['username']);
        $resul->bindValue(":role", $data['role']);
        $resul->bindValue(":correo", $data['correo']);
        $resul->bindValue(":status", $data['status']);
        
        return $resul->execute();
    }

    //Cambio de contraseña
    public function change_password($data)
    {
        $resul = $this->_db->prepare("UPDATE users SET password = :password, hash_key = :key , password_change_datetime = DATE_ADD(NOW(), INTERVAL 1 MONTH) WHERE username = :username");

        $resul->bindValue(":password", $data['password']);
        $resul->bindValue(":key", $data['key']);
        $resul->bindValue(":username", Session::get("Mundial_authenticate_username"));

        return $resul->execute();
    }

    //Cambio de contraseña
    public function change_password_admin($data)
    {
        $resul = $this->_db->prepare("UPDATE users SET password = :password, hash_key = :key , password_change_datetime = DATE_ADD(NOW(), INTERVAL 1 MONTH) WHERE id = :id");

        $resul->bindValue(":password", $data['password']);
        $resul->bindValue(":key", $data['key']);
        $resul->bindValue(":id", $data['id']);

        return $resul->execute();
    }

    //Bloquear usuario por clave usuario
    public function bloquear($data)
    {
        $resul = $this->_db->prepare("UPDATE users SET status = :bloq WHERE username = :username");
        $resul->bindValue(":bloq", 0);
        $resul->bindValue(":username", $data['username']);

        return $resul->execute();
    }

    //Eliminar usuario
    public function delete($id)
    {
        /*
        $result = $this->_db->prepare("UPDATE users SET status = 0 WHERE id = :id");
        $result->bindValue(":id", $id);

        $result->execute();

        if(!is_null($result->errorInfo()[2]))
            return false;
        else
            return true;
        */
        $resul = $this->_db->prepare("DELETE FROM users WHERE id = :id");
        $resul->bindValue(":id", $id);

        return $resul->execute();
    }


    //Obtener información de usuario por id
    public function getUserInfo($id)
    {
        $resul = $this->_db->prepare("SELECT * FROM users WHERE id = :id");
        $resul->bindValue(":id", $id);

        $resul->execute();
        return $resul->fetch(PDO::FETCH_ASSOC);
    }

    public function iniciarSid($sid,$id)
    {
      $resul = $this->_db->prepare("UPDATE users SET sid = :sid WHERE id = :id");
      $resul->bindValue(":sid", $sid);
      $resul->bindValue(":id", $id);

      return $resul->execute();
    }
}