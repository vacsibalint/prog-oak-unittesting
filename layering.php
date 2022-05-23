<?php
//Controller szint
class IndexController {
    private $userService;

    public function __construct(UserService $us) {
        $this->userService = $us;
    }

    // Assume the index page requires a User object
    public function indexAction() {
        $user = $this->userService->getById(123456);

        // return the object to the view
    }
}

//Service szint (a service(k)-ből készül a kontroller)
interface UserService {
    public function getById($id);
}

class UserServiceImpl implements UserService {
    private $userDAO;

    public function __construct(UserDAO $dao) {
        $this->userDAO = $dao;
    }

    public function getById($id) {
        $user = new User();
        $userDTO = $this->userDAO->getById($id);

        $user->setDTO($userDTO);

        return $user;
    }
}

//DAO szint (data access object)
interface UserDAO {
    public function getById($id);
}

class UserDAOImpl implements UserDAO {

    public function getById($id) {
        // Doctrine, Propel, web service..

        $dto = new UserDTO();
        $dto->setFirstName('John');
        $dto->setLastName('Doe');
        $dto->setLevel(2);

        return $dto;
    }
}

//DTO szint (data transfer object - "modellek")
Class UserDTO {
    private $firstName;
    private $lastName;
    private $level;

    public function getFirstName() {
        return $this->firstName;
    }

    public function setFirstName($firstName) {
        $this->firstName = $firstName;
    }

    public function getLastName() {
        return $this->lastName;
    }

    public function setLastName($lastName) {
        $this->lastName = $lastName;
    }

    public function getLevel() {
        return $this->level;
    }

    public function setLevel($level) {
        $this->level = $level;
    }
}

//Business logic
class User {
    private $dto;

    public function setDTO(UserDTO $dto) {
        $this->dto = $dto;
    }

    public function getDTO() {
        return $this->dto;
    }

    public function isStaff() {
        return $this->dto->getLevel() > 0;
    }

    public function hasLastName() {
        return $this->dto->getLastName() !== null;
    }
}