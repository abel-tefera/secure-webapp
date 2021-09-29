<?php
define("RECAPTCHA_V3_SECRET_KEY", '6Ld6XZMcAAAAADJ5eJ5P7BmyOHk27FEh1G8wMVtO');

class Complaints extends Controller
{
  public function __construct()
  {
    if (!isset($_SESSION['user_id'])) {
      redirect('users/login');
    }
    // Load Models
    $this->complaintModel = $this->model('Complaint');
    $this->userModel = $this->model('User');
  }

  private function checkIfNotAdmin()
  {
    if ($_SESSION['user_role'] == 1) {
      redirect('complaints');
      return false;
    } else {
      return true;
    }
  }
  private function checkAntiCSRF()
  {
    // console_log($_SESSION['token']);
    if (hash_equals($_SESSION['token'], $_POST['token'])) {
      return true;
    } else {
      $this->logoutCallback();

      return false;
    }
  }
  private function checkUserStatus()
  {
    $status = $this->userModel->getUserStatus($_SESSION['user_id']);
    if ($status == 1) {
      return true;
    } else {
      $this->logoutCallback();
      return false;
    }
  }

  private function logoutCallback()
  {
    unset($_SESSION['user_id']);
    unset($_SESSION['user_email']);
    unset($_SESSION['user_name']);
    unset($_SESSION['user_role']);
    unset($_SESSION['token']);

    if (isset($_COOKIE[session_name()])) {
      setcookie(session_name(), '', time() - 7000000, '/');
    }
    
    session_destroy();
    redirect('users/login');
  }

  // Load All Complaints
  public function index()
  {
    $this->checkUserStatus();
    if ($_SESSION['user_role'] == 1) {
      $complaints = $this->complaintModel->getComplaints();
      $data = [
        'complaints' => $complaints
      ];
    } else if ($_SESSION['user_role'] == 0) {
      $complaints = $this->complaintModel->getComplaintsByUser($_SESSION['user_id']);
      $data = [
        'complaints' => $complaints
      ];
    }

    // console_log($data['complaints']);

    $this->view('complaints/index', $data);
  }

  // Show Single Complaint
  public function show($id)
  {
    $complaint = $this->complaintModel->getComplaintById($id);
    $user = $this->userModel->getUserById($complaint->user_id);

    if ($_SESSION['user_role'] == 1) {
      $data = [
        'complaint' => $complaint,
        'user' => $user
      ];
      $this->view('complaints/show', $data);
    } else {
      if ($_SESSION['user_id'] == $user->id) {
        $data = [
          'complaint' => $complaint,
          'user' => $user
        ];
        $this->view('complaints/show', $data);
      } else {
        redirect('complaints');
      }
    }
  }

  // Add Complaint
  public function add()
  {
    $this->checkUserStatus();
    $this->checkIfNotAdmin();
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $this->checkAntiCSRF();

      // Sanitize POST
      $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      $token = $_POST['reCtoken'];
      $action = $_POST['reCaction'];

      // call curl to POST request
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('secret' => RECAPTCHA_V3_SECRET_KEY, 'response' => $token)));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $response = curl_exec($ch);
      curl_close($ch);
      $arrResponse = json_decode($response, true);
      // verify the response
      if ($arrResponse["success"] == '1' && $arrResponse["action"] == $action && $arrResponse["score"] >= 0.5) {
      } else {
        $this->logoutCallback();
        return false;
      }

      $data = [
        'title' => htmlentities(trim($_POST['title'])),
        'body' => htmlentities(trim($_POST['body'])),
        'user_id' => $_SESSION['user_id'],
        'title_err' => '',
        'body_err' => '',
        'upload_err' => ''
      ];

      // Validate email
      if (empty($data['title'])) {
        $data['title_err'] = 'Please enter name';
        // Validate name
      }

      if (empty($data['body'])) {
        $data['body_err'] = 'Please enter the complaint body';
      }

      // Make sure there are no errors
      if (empty($data['title_err']) && empty($data['body_err'])) {
        // Validation passed
        //Execute
        if ($_FILES["fileToUpload"]["name"] !== '') {
          $ret = upload();
          if ($ret[0] == 0) {
            $data['upload_err'] = $ret[1];
            $this->view('complaints/add', $data);
          } else {
            // echo $ret[2];
            $data['file_name'] = $ret[2];
            if ($this->complaintModel->addComplaint($data)) {
              flash('complaint_message', 'Complaint Added and File Uploaded');
              redirect('complaints');
            } else {
              die('Something went wrong');
            }
          }
        } else {
          if ($this->complaintModel->addComplaint($data)) {
            flash('complaint_message', 'Complaint Added');
            redirect('complaints');
          } else {
            die('Something went wrong');
          }
        }
      } else {
        // Load view with errors
        $this->view('complaints/add', $data);
      }
    } else {
      $data = [
        'title' => '',
        'body' => '',
      ];

      $this->view('complaints/add', $data);
    }
  }

  // Edit Complaint
  public function edit($id)
  {
    $this->checkUserStatus();
    $this->checkIfNotAdmin();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $this->checkAntiCSRF();

      $complaint = $this->complaintModel->getComplaintById($id);
      if ($complaint->user_id != $_SESSION['user_id']) {
        redirect('complaints');
      }
      // Sanitize POST
      $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      $data = [
        'id' => $id,
        'title' => htmlentities(trim($_POST['title'])),
        'body' => htmlentities(trim($_POST['body'])),
        'user_id' => $_SESSION['user_id'],
        'title_err' => '',
        'body_err' => '',
        'upload_err' => ''
      ];

      // Validate email
      if (empty($data['title'])) {
        $data['title_err'] = 'Please enter name';
        // Validate name
      }

      if (empty($data['body'])) {
        $data['body_err'] = 'Please enter the complaint body';
      }

      // Make sure there are no errors
      if (empty($data['title_err']) && empty($data['body_err'])) {
        // Validation passed
        //Execute
        if ($_FILES["fileToUpload"]["name"] !== '') {
          $ret = upload();
          if ($ret[0] == 0) {
            $data['upload_err'] = $ret[1];
            $this->view('complaints/edit', $data);
          } else {
            $data['file_name'] = $ret[2];
            if ($this->complaintModel->updateComplaint($data)) {
              flash('complaint_message', 'Complaint Updated with File');
              redirect('complaints');
            } else {
              die('Something went wrong');
            }
          }
        } else {
          if ($this->complaintModel->updateComplaint($data)) {
            flash('complaint_message', 'Complaint Updated');
            redirect('complaints');
          } else {
            die('Something went wrong');
          }
        }
      } else {
        // Load view with errors
        $this->view('complaints/edit', $data);
      }
    } else {
      // Get complaint from model
      $complaint = $this->complaintModel->getComplaintById($id);

      // Check for owner
      if ($complaint->user_id != $_SESSION['user_id']) {
        redirect('complaints');
      }

      $data = [
        'id' => $id,
        'title' => $complaint->title,
        'body' => $complaint->body,
      ];

      $this->view('complaints/edit', $data);
    }
  }

  // Delete Complaint
  public function delete($id)
  {
    $this->checkUserStatus();
    $this->checkIfNotAdmin();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $this->checkAntiCSRF();

      $complaint = $this->complaintModel->getComplaintById($id);
      if ($_SESSION['user_id'] != $complaint->user_id) {
        redirect('complaints');
      }

      if ($this->complaintModel->deleteComplaint($id)) {
        flash('complaint_message', 'Complaint Removed');
        redirect('complaints');
      } else {
        die('Something went wrong');
      }
    } else {
      redirect('complaints');
    }
  }

  public function download($filename)
  {
    $this->checkUserStatus();
    if ($_SESSION['user_role'] == 1) {
      $this->readFile($filename);
    } else {
      $cps = $this->complaintModel->getComplaintsByUser($_SESSION['user_id']);
      foreach ($cps as $cp) {
        if ($cp->file_name == $filename) {
          $this->readFile($filename);
          break;
        }
      }
    }
  }

  private function readFile($filename)
  {
    header("Content-type: application/pdf");
    header("Content-Transfer-Encoding: binary");
    header('Pragma: no-cache');
    header('Expires: 0');
    // header('Content-Disposition: attachment');
    set_time_limit(0);
    ob_clean();
    flush();
    readfile('/var/secure_files/' . $filename);
  }
}
