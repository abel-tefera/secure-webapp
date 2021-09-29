<?php
class User
{
  private $db;

  public function __construct()
  {
    $this->db = new Database;
  }

  // Add User / Register
  public function register($data)
  {
    // Prepare Query
    $this->db->query('INSERT INTO users (name, email,password) 
      VALUES (:name, :email, :password)');

    // Bind Values
    $this->db->bind(':name', $data['name']);
    $this->db->bind(':email', $data['email']);
    $this->db->bind(':password', $data['password']);

    //Execute
    if ($this->db->execute()) {
      // return true;
      $this->db->query("SELECT * FROM users WHERE email = :email");
      $this->db->bind(':email', $data['email']);
      $row = $this->db->single();
      return $row;
    } else {
      return false;
    }
  }

  // Find USer BY Email
  public function findUserByEmail($email)
  {
    $this->db->query("SELECT * FROM users WHERE email = :email");
    $this->db->bind(':email', $email);

    $row = $this->db->single();

    //Check Rows
    if ($this->db->rowCount() > 0) {
      return true;
    } else {
      return false;
    }
  }

  public function findAllUsers()
  {
    $this->db->query("SELECT name, id, email, status, created_at FROM users ORDER BY created_at DESC");
    $results = $this->db->resultset();
    return $results;
  }

  // Login / Authenticate User
  public function login($email, $password)
  {
    $this->db->query("SELECT * FROM users WHERE email = :email");
    $this->db->bind(':email', $email);

    $row = $this->db->single();

    $hashed_password = $row->password;
    sleep(1);
    if (password_verify($password, $hashed_password)) {
      if ($row->status == 1) {
        // if ($row->failed_attempts > 0) {
        //   $this->resetFailedAttempt($row->id);
        // }
        return [$row, true];
      } else {
        $this->incFailedAttempt($row->id, $row->failed_attempts);
        return [0, false];
      }
    } else {
      $this->incFailedAttempt($row->id, $row->failed_attempts);
      return [1, false];
    }
  }

  private function incFailedAttempt($id, $count)
  {
    $this->db->query('UPDATE users SET failed_attempts = :failed_attempts WHERE id = :id');
    $this->db->bind(':id', $id);
    $this->db->bind(':failed_attempts', $count + 1);
    $this->db->execute();
  }

  public function resetFailedAttempt($id)
  {
    $this->db->query('UPDATE users SET failed_attempts = 0 WHERE id = :id');
    $this->db->bind(':id', $id);
    $this->db->execute();
  }

  // Find User By ID
  public function getUserById($id)
  {
    $this->db->query("SELECT * FROM users WHERE id = :id");
    $this->db->bind(':id', $id);

    $row = $this->db->single();

    return $row;
  }

  public function getUserQuestionsById($id)
  {
    $this->db->query("SELECT questions, answers FROM users WHERE id = :id");
    $this->db->bind(':id', $id);

    $row = $this->db->single();

    return $row;
  }

  public function getUserStatus($id)
  {
    $this->db->query("SELECT status FROM users WHERE id = :id");
    $this->db->bind(':id', $id);

    $row = $this->db->single();

    return $row->status;
  }

  public function setUserStatus($id, $status)
  {
    $this->db->query('UPDATE users SET status = :status WHERE id = :id');

    // Bind Values
    $this->db->bind(':id', $id);
    $this->db->bind(':status', $status);

    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }

  public function getSecretQuestions()
  {
    $this->db->query("SELECT * FROM secret_questions");
    $results = $this->db->resultset();
    return $results;
  }

  public function updateSecurityQuestions($data){
    $qs = $data["sec1-select"] . "," . $data["sec2-select"] . "," . $data["sec3-select"];
    $as = trim($data["sec1"]) . "," . trim($data["sec2"]) . "," . trim($data["sec3"]);
    
    $this->db->query('UPDATE users set questions = :questions, answers = :answers WHERE id = :id');
    $this->db->bind(':questions', strval($qs));
    $this->db->bind(':answers', strval($as));
    $this->db->bind(':id', $data['user_id']);
    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }
}
