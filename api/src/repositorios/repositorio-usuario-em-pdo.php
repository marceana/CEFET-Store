<?php

require_once("repositorio-usuario.php");
require_once("./src/exceptions/repositorio-usuario-exception.php");
require_once("./src/usuario.php");
class RepositorioUsuarioEmPDO implements RepositorioUsuario {
    private $pdo;
    public function __construct(PDO $pdo) {
        $this->pdo=$pdo;
    }
    public function login(Usuario $usuario) {
        $sql = 'SELECT * from usuario WHERE email = :em OR matricula=:matr';
        $email = $usuario->getEmail();
        $matricula = $usuario->getMatricula();
        try{
            $ps = $this->pdo->prepare($sql);
            $ps->setFetchMode(PDO::FETCH_ASSOC);
            $ps->bindParam(':em', $email, PDO::PARAM_STR);
            $ps->bindParam(':matr', $matricula, PDO::PARAM_STR);
            $ps->execute();
            if($ps->rowCount()<=0) {
                return null;
            }  
            $usuarioObtido = $ps->fetch();
            if (!password_verify( $usuario->getSenha(), $usuarioObtido['senha'])) {
                return false;
            } 
            $usuario->setId(intval($usuarioObtido['id']));
            $usuario->setMatricula($usuarioObtido['matricula']);
            $usuario->setNome($usuarioObtido['nome']);
            $usuario->setEmail($usuarioObtido['email']);
            $usuario->setSaldo(floatval(number_format($usuarioObtido['saldo'],2)));
            return $usuario;           
        }catch(PDOException $e) {
           throw new RepositorioUsuarioException("Não foi possível obter o usuario.".$e->getMessage());
        }
    }
    public function obterPorId($idUsuario) {
        $sql = 'SELECT * from usuario WHERE id=:idUsuario';
        try{
            $ps = $this->pdo->prepare($sql);
            $ps->setFetchMode(PDO::FETCH_ASSOC);
            $ps->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
            $ps->execute();
            if($ps->rowCount()<=0) {
                return null;
            } 
            $usuarioObtido = $ps->fetch();
            $usuario = new Usuario($usuarioObtido['matricula'], $usuarioObtido['nome'], $usuarioObtido['email'], 
                $usuarioObtido['senha'], floatval(number_format($usuarioObtido['saldo'],2)),intval($usuarioObtido['id']),);
            return $usuario;           
        }catch(PDOException $e) {
           throw new RepositorioUsuarioException("Não foi possível obter o usuario.".$e->getMessage());
        }
    }
}

?>