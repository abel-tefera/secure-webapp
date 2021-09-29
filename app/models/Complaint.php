<?php
class Complaint
{
  private $db;

  public function __construct()
  {
    $this->db = new Database;
  }

  // Get All Complaints
  public function getComplaints()
  {
    // if ($id) {
    //   $this->db->query("SELECT *
    //   FROM complaints
    //   WHERE user_id = 2
    //   ORDER BY complaints.created_at DESC");
    //   // $this->db->bind(':id', $id);
    // } else {

    // }
    $this->db->query("SELECT title, body, email, file_name, name, complaints.created_at,
    complaints.id as complaintId, 
    users.id as userId
    FROM complaints 
    INNER JOIN users 
    ON complaints.user_id = users.id
    ORDER BY complaints.created_at DESC;");
    $results = $this->db->resultset();
    return $results;
  }

  public function getComplaintsByUser($userId)
  {
    $this->db->query("SELECT title, body, email, file_name, name, complaints.created_at,
    complaints.id as complaintId, 
    users.id as userId
    FROM complaints, users
    WHERE complaints.user_id = :userOId AND complaints.user_id = users.id 
    -- INNER JOIN  
    -- ON complaints.user_id = users.id
    ORDER BY complaints.created_at DESC;");
    $this->db->bind(':userOId', $userId);
    $results = $this->db->resultset();
    return $results;
  }

  // Get Complaint By ID
  public function getComplaintById($id)
  {
    $this->db->query("SELECT * FROM complaints WHERE id = :id");

    $this->db->bind(':id', $id);

    $row = $this->db->single();

    return $row;
  }

  // Add Complaint
  public function addComplaint($data)
  {
    // Prepare Query
    $this->db->query('INSERT INTO complaints (title, user_id, body, file_name) 
      VALUES (:title, :user_id, :body, :file_name)');

    // Bind Values
    $this->db->bind(':title', $data['title']);
    $this->db->bind(':user_id', $data['user_id']);
    $this->db->bind(':body', $data['body']);
    $this->db->bind(':file_name', $data['file_name']);

    //Execute
    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }

  // Update Complaint
  public function updateComplaint($data)
  {
    // Prepare Query
    if ($data['file_name']) {
      $this->db->query('UPDATE complaints SET title = :title, body = :body, file_name = :file_name WHERE id = :id');
      $this->db->bind(':file_name', $data['file_name']);
    } else {
      $this->db->query('UPDATE complaints SET title = :title, body = :body WHERE id = :id');
    }
    // Bind Values
    $this->db->bind(':id', $data['id']);
    $this->db->bind(':title', $data['title']);
    $this->db->bind(':body', $data['body']);

    //Execute
    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }

  // Delete Complaint
  public function deleteComplaint($id)
  {
    // Prepare Query
    $this->db->query('DELETE FROM complaints WHERE id = :id');

    // Bind Values
    $this->db->bind(':id', $id);

    //Execute
    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }

  // public function uploadFile($dir, $id)
  // {
  //   $this->db->query('UPDATE complaints SET file_directory = :dir WHERE id = :id');
  //   $this->db->bind(':id', $id);
  //   $this->db->bind(':dir', $dir);
  //   return $this->db->execute();
  // }
}
