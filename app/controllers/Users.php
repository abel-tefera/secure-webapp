<?php
class Users extends Controller
{
  public function __construct()
  {
    $this->userModel = $this->model('User');
  }

  public function index()
  {
    redirect('welcome');
  }

  private function checkUserStatus()
  {
    $status = $this->userModel->getUserStatus($_SESSION['user_id']);
    if ($status == 1) {
      return true;
    } else {
      $this->logout();
      return false;
    }
  }

  private function checkPasswordStrength($password)
  {
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number    = preg_match('@[0-9]@', $password);
    $specialChars = preg_match('@[^\w]@', $password);

    if (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
      return false;
    } else {
      return true;
    }
  }
  public function register()
  {
    // Check if logged in
    if ($this->isLoggedIn()) {
      redirect('complaints');
    }

    // Check if POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Sanitize POST
      $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      $data = [
        'name' => trim($_POST['name']),
        'email' => trim($_POST['email']),
        'password' => trim($_POST['password']),
        'confirm_password' => trim($_POST['confirm_password']),
        'name_err' => '',
        'email_err' => '',
        'password_err' => '',
        'confirm_password_err' => ''
      ];

      // Validate email
      if (empty($data['email'])) {
        $data['email_err'] = 'Please enter an email';
        // Validate name
      } else if ($this->userModel->findUserByEmail($data['email'])) {
        $data['email_err'] = 'Email is already taken.';
      }
      if (empty($data['name'])) {
        $data['name_err'] = 'Please enter a name';
      }


      // Validate password
      // if (empty($data['password'])) {
      //   $data['$password_err'] = 'Please enter a password.';
      // } elseif (strlen($data['password']) < 6) {
      //   $data['password_err'] = 'Password must have atleast 6 characters.';
      // }

      // Validate confirm password
      if (empty($data['confirm_password'])) {
        $data['confirm_password_err'] = 'Please confirm password.';
      } else {
        if ($data['password'] != $data['confirm_password']) {
          $data['confirm_password_err'] = 'Password do not match.';
        }
      }

      if (!$this->checkPasswordStrength($data['password'])) {
        $data['password_err'] = 'Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.';
      }
      // Make sure errors are empty
      if (empty($data['name_err']) && empty($data['email_err']) && empty($data['password_err']) && empty($data['confirm_password_err'])) {
        // SUCCESS - Proceed to insert

        // Hash Password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        //Execute
        $user = $this->userModel->register($data);
        if ($user) {
          // Redirect to login
          // flash('register_success', 'You are now registered and can log in');
          // redirect('users/login');
          $this->createUserSession($user);
        } else {
          die('Something went wrong');
        }
      } else {
        // Load View
        $this->view('users/register', $data);
      }
    } else {
      // IF NOT A POST REQUEST

      // Init data
      $data = [
        'name' => '',
        'email' => '',
        'password' => '',
        'confirm_password' => '',
        'name_err' => '',
        'email_err' => '',
        'password_err' => '',
        'confirm_password_err' => ''
      ];

      // Load View
      $this->view('users/register', $data);
    }
  }

  public function login()
  {
    // Check if logged in
    if ($this->isLoggedIn()) {
      redirect('complaints');
    }

    // Check if POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Sanitize POST
      $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      $data = [
        'email' => trim($_POST['email']),
        'password' => trim($_POST['password']),
        'email_err' => '',
        'password_err' => '',
      ];

      // Check for email
      if (empty($data['email'])) {
        $data['email_err'] = 'Please enter email.';
      }

      // Check for name
      if (empty($data['name'])) {
        $data['name_err'] = 'Please enter name.';
      }

      // Check for user
      if ($this->userModel->findUserByEmail($data['email'])) {
        // User Found
      } else {
        // No User
        $data['email_err'] = 'This email is not registered.';
      }

      // Make sure errors are empty
      if (empty($data['email_err']) && empty($data['password_err'])) {

        // Check and set logged in user
        $loggedInUser = $this->userModel->login($data['email'], $data['password']);
        $lockedOutStr = 'You have been locked out of your account. Please contact Administrator.';
        if ($loggedInUser[1]) {
          // User Authenticated!
          // console_log($loggedInUser[0]);
          if ($loggedInUser[0]->failed_attempts >= 5) {
            if ($loggedInUser[0]->questions !== null) {
              $_SESSION['askQ'] = $loggedInUser[0]->id;
              redirect('users/verify');
            } else {
              flash('register_success', $lockedOutStr, 'alert alert-danger');
              $this->userModel->setUserStatus($loggedInUser[0]->id, 0);
              $this->view('users/login', $data);
            }

            // $this->view('users/verify', $loggedInUser[0]);
          } else {
            $this->userModel->resetFailedAttempt($loggedInUser[0]->id);
            $this->createUserSession($loggedInUser[0]);
          }
        } else {
          if ($loggedInUser[0] == 1) {
            $data['password_err'] = 'Password incorrect.';
          } else if ($loggedInUser[0] == 0) {
            // $data['password_err'] = 'You are Not Authorized to use website.';
            flash('register_success', $lockedOutStr, 'alert alert-danger');
          }
          // Load View
          $this->view('users/login', $data);
        }
      } else {
        // Load View
        $this->view('users/login', $data);
      }
    } else {
      // If NOT a POST

      // Init data
      $data = [
        'email' => '',
        'password' => '',
        'email_err' => '',
        'password_err' => '',
      ];

      // Load View
      $this->view('users/login', $data);
    }
  }

  public function verify()
  {
    if (isset($_SESSION['user_id'])) {
      redirect('complaints');
    } else if (isset($_SESSION['askQ'])) {
      $questions = $this->userModel->getSecretQuestions();
      $user = $this->userModel->getUserById($_SESSION['askQ']);

      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Sanitize POST
        $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $data = [
          'questions' => $questions,
          'qIds' => explode(',', $user->questions),
          'answers' => $user->answers,
          'sec1' => trim($_POST['sec1']),
          'sec2' => trim($_POST['sec2']),
          'sec3' => trim($_POST['sec3']),
        ];

        $ans = explode(",", $data['answers']);
        if ((!strcasecmp(trim($data["sec1"]), $ans[0]) == 0) || (!strcasecmp(trim($data["sec2"]), $ans[1]) == 0) || (!strcasecmp(trim($data["sec3"]), $ans[2]) == 0)) {
          flash('verify_fail', 'One or more of the answers is incorrect', 'alert alert-danger');
          $this->view('users/verify', $data);
        } else {
          $this->userModel->resetFailedAttempt($_SESSION['askQ']);
          $this->createUserSession($user);
        }
      } else {
        $data = [
          'questions' => $questions,
          'qIds' => explode(',', $user->questions),
          'answers' => $user->answers,
          'sec1' => '',
          'sec2' => '',
          'sec3' => '',
        ];
        $this->view('users/verify', $data);
      }
    } else {
      redirect('index');
    }
  }
  private function checkForSpecialChars($string)
  {
    if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $string)) {
      return true;
    } else {
      return false;
    }
  }
  public function settings()
  {
    if (!$this->isLoggedIn()) {
      redirect('index');
    }
    $this->checkUserStatus();
    $questions = $this->userModel->getSecretQuestions();
    $userQA = $this->userModel->getUserQuestionsById($_SESSION['user_id']);
    $ans = explode(',', $userQA->answers);
    $sel = explode(',', $userQA->questions);
    // console_log($userQA);
    // Check if POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Sanitize POST
      $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      $data = [
        'questions' => $questions,
        'user_id' => $_SESSION['user_id'],
        'sec1' => trim($_POST['sec1']),
        'sec1-select' => trim($_POST['sec1-select']),
        'sec2' => trim($_POST['sec2']),
        'sec2-select' => trim($_POST['sec2-select']),
        'sec3' => trim($_POST['sec3']),
        'sec3-select' => trim($_POST['sec3-select']),
        'sec1-err' => '',
        'sec2-err' => '',
        'sec3-err' => '',
      ];
      $errStr = 'Special characters are not allowed';

      if (empty($data['sec1'])) {
        $data['sec1_err'] = 'Please enter answer for first question';
      } else if ($this->checkForSpecialChars($data['sec1'])) {
        $data['sec1_err'] = $errStr;
      }
      if (empty($data['sec2'])) {
        $data['sec2_err'] = 'Please enter answer for second question';
      } else if ($this->checkForSpecialChars($data['sec2'])) {
        $data['sec2_err'] = $errStr;
      }
      if (empty($data['sec3'])) {
        $data['sec3_err'] = 'Please enter answer for third question';
      } else if ($this->checkForSpecialChars($data['sec3'])) {
        $data['sec3_err'] = $errStr;
      }
      if (empty($data['sec1_err']) && empty($data['sec2_err']) && empty($data['sec3_err'])) {
        if ($this->userModel->updateSecurityQuestions($data)) {
          flash('complaint_message', 'Security Questions Updated');
          redirect('complaints');
        } else {
          die('Something went wrong');
        }
      } else {
        // Load view with errors
        $this->view('users/settings', $data);
      }
    } else {
      $data = [
        'questions' => $questions,
        'sec1' => $ans[0],
        'sec2' => $ans[1],
        'sec3' => $ans[2],
        'sec1-select' => $sel[0],
        'sec2-select' => $sel[1],
        'sec3-select' => $sel[2],
      ];
      // $data['questions'] = $this->userModel->getSecretQuestions();
      $this->view('users/settings', $data);
    }
  }

  // Create Session With User Info
  public function createUserSession($user)
  {
    session_regenerate_id();
    $_SESSION['user_id'] = $user->id;
    $_SESSION['user_email'] = $user->email;
    $_SESSION['user_name'] = $user->name;
    $_SESSION['user_role'] = $user->role;
    $_SESSION['token'] = bin2hex(random_bytes(24));
    unset($_SESSION['askQ']);
    redirect('complaints');
  }

  // Logout & Destroy Session
  public function logout()
  {
    unset($_SESSION['user_id']);
    unset($_SESSION['user_email']);
    unset($_SESSION['user_name']);
    unset($_SESSION['user_role']);
    unset($_SESSION['askQ']);
    unset($_SESSION['token']);

    if (isset($_COOKIE[session_name()])) {
      setcookie(session_name(), '', time() - 7000000, '/');
    }
    
    session_destroy();
    redirect('users/login');
  }

  // Check Logged In
  public function isLoggedIn()
  {
    if (isset($_SESSION['user_id'])) {
      return true;
    } else {
      return false;
    }
  }
}
