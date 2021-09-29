<?php
class Pages extends Controller
{
  public function __construct()
  {
    $this->userModel = $this->model('User');
  }

  // Load Homepage
  public function index()
  {
    // If logged in, redirect to complaints
    if (isset($_SESSION['user_id'])) {
      redirect('complaints');
    }

    //Set Data
    $data = [
      'title' => 'Welcome To Addis Ababa Complaints',
      'description' => 'Secure Web App made using custom PHP MVC framework',
    ];

    // Load homepage/index view
    $this->view('pages/index', $data);
  }

  public function about()
  {
    //Set Data
    $data = [
      'title' => 'This website is secure against the following web security vulnerabilities:',
      'features' => [
        'Authentication, Authorization, User Management, Session Management',
        'Remote/Local Code Execution(RCE/LCE), Remote File Include',
        'Cross Site Scripting (XSS), Cross Site Request Forgery (CSRF), SQL Injection(SQLi)',
        'Businesss Logic & Concurrency',
        'Input Validaton', 'Anti-automation', 'Safe Redirection'
      ],
    ];

    // Load about view
    $this->view('pages/about', $data);
  }

  public function accounts()
  {
    if (isset($_SESSION['user_id']) && ($_SESSION['user_role'] == 1)) {
      $data = $this->userModel->findAllUsers();
      $this->view('pages/accounts', $data);
    } elseif (isset($_SESSION['user_id'])) {
      redirect('complaints');
    } else {
      redirect('index');
    }
  }
  public function setStatus($id)
  {
    if (isset($_SESSION['user_id']) && ($_SESSION['user_role'] == 1)) {
      $status = $this->userModel->getUserStatus($id);
      if ($status == 0) {
        $result = $this->userModel->setUserStatus($id, 1);
        $this->userModel->resetFailedAttempt($id);
      } elseif ($status == 1) {
        $result = $this->userModel->setUserStatus($id, 0);
      }
      // if ($result) {
      //   flash('accounts_msg', 'Successfully updated status', 'alert alert-success');
      // } else {
      //   flash('accounts_msg', 'Failed to update status', 'alert alert-danger');
      // }
      redirect('pages/accounts');
    } elseif (isset($_SESSION['user_id'])) {
      redirect('complaints');
    } else {
      redirect('index');
    }
  }
}
