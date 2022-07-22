 <?php
    function clean($str)
    {
        return htmlentities($str);
    }


    function  redirect($location)
    {
        header(header: "location: {$location}");
        exit();
    }




    function set_message($message)
    {
        if (!empty($message)) {
            $_SESSION['message'] = $message;
        } else {

            $message = "";
        }
    }


    function  display_message()
    {
        if (isset($_SESSION['message'])) {
            echo $_SESSION['message'];
            unset($_SESSION['message']);
        }
    }


    function email_exists($email)
    {
        $email = filter_var($email, filter: FILTER_SANITIZE_EMAIL);
        $query = "SELECT id FROM users where email = '$email' ";
        $result = query($query);

        if ($result->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }


    function user_exists($user)
    {
        $user = filter_var($user, filter: FILTER_SANITIZE_STRING);
        $query = "SELECT id FROM users WHERE  username ='$user'";
        $result = query($query);

        if ($result->num_rows > 0) {
            return true;
        }

        return false;
    }



    function validate_user_registration()
    {
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $first_name = clean($_POST['first_name']);
            $last_name = clean($_POST['last_name']);
            $username = clean($_POST['username']);
            $email = clean($_POST['email']);
            $password = clean($_POST['password']);
            $confirm_password = clean($_POST['confirm_password']);


            if (strlen($first_name) < 3) {
                $errors[] = "Your First Name cannot be less then 3 characters!";
            }

            if (strlen($last_name) < 3) {
                $errors[] = "Your Last Name cannot be less then 3 characters!";
            }

            if (strlen($username) < 3) {
                $errors[] = "Your Username cannot be less then 3 characters!";
            }

            if (strlen($username) > 20) {
                $errors[] = "Your Username cannot be bigger then 20 characters!";
            }

            if (email_exists($email)) {
                $errors[] = "Sorry that Email is already is taken";
            }

            if (user_exists($username)) {
                $errors[] = "Sorry that Username is already is taken";
            }

            if (strlen($password) < 8) {
                $errors[] = "Your Password cannot be less then 8 characters";
            }

            if ($password != $confirm_password) {
                $errors[] = "The password was not confired correctly";
            }

            if (!empty($errors)) {
                foreach ($errors as $error) {
                    echo "<div class='alert'>" . $error . "</div>";
                }
            } else {

                $first_name = filter_var($first_name, filter: FILTER_SANITIZE_STRING);
                $last_name = filter_var($last_name, filter: FILTER_SANITIZE_STRING);
                $username = filter_var($username, filter: FILTER_SANITIZE_STRING);
                $email = filter_var($email, filter: FILTER_SANITIZE_EMAIL);
                $password = filter_var($password, filter: FILTER_SANITIZE_STRING);

                create_user($first_name, $last_name, $username, $email, $password);
            }
        }
    }




    function create_user($first_name, $last_name, $username, $email, $password)
    {


        $first_name = escape($first_name);
        $last_name = escape($last_name);
        $username = escape($username);
        $email = escape($email);
        $password = escape($password);
        $password = password_hash($password, algo: PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (first_name, last_name, username, profile_image, email, password)";
        $sql .= "VALUES ('$first_name', '$last_name', '$username', 'uploads/default.jpg', '$email', '$password')";


        confrm(query($sql));
        set_message("You have been successfuly  registered! Please Log in!");
        redirect("login.php");
    }


    function  validate_user_login()
    {
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = clean($_POST['email']);
            $password = clean($_POST['password']);

            if (empty($email)) {
                $errors[] = "Email field cannot be empty";
            }

            if (empty($password)) {
                $errors[] = "Password field cannot be empty";
            }

            if (empty($errors)) {
                if (user_login($email, $password)) {
                    redirect(location: "index.php");
                } else {
                    $errors[] = "Your email or password is incorrect. Please try again";
                }
            }

            if (!empty($errors)) {
                foreach ($errors as $error) {
                    echo '<div class="alert">' . $error . '</div>';
                }
            }
        }
    }


    function user_login($email, $password)
    {
        $password = filter_var($password, filter: FILTER_SANITIZE_STRING);
        $email = filter_var($email, filter: FILTER_SANITIZE_EMAIL);

        $query = "SELECT * FROM users WHERE email='$email'";
        $result = query($query);

        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();

            if (password_verify($password, $data['password'])) {
                $_SESSION['email'] = $email;

                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    function getUser($id = NULL)
    {
        if ($id != NULL) {
            $query = "SELECT * FROM users WHERE id =" . $id;
            $result = query($query);

            if ($result->num_rows > 0) {
                return $result->fetch_assoc();
            } else {
                return "User not found.";
            }
        } else {
            $query = "SELECT * FROM users WHERE email='" . $_SESSION['email'] . "'";
            $result = query($query);

            if ($result->num_rows > 0) {
                return $result->fetch_assoc();
            } else {
                return "User not found";
            }
        }
    }


    function user_profile_image_upload()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $target_dir = 'uploads/';
            $user = getUser();
            $user_id = $user['id'];
            $target_file = $target_dir . $user_id . "." . pathinfo(basename($_FILES['profile_image_file']['name']), flags: PATHINFO_EXTENSION);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, flags: PATHINFO_EXTENSION));
            $error = "";

            $chek = getimagesize($_FILES['profile_image_file']['tmp_name']);

            if ($chek !== false) {
                $uploadOk = 1;
            } else {
                $error = "File is not an image.";
                $uploadOk = 0;
            }

            if ($_FILES['profile_image_file']['size'] > 5000000) {
                $error = "Sorry, your file is too large.";
                $uploadOk = 0;
            }

            if ($imageFileType != 'jpg' && $imageFileType != 'png' && $imageFileType != 'jpeg' && $imageFileType != 'gif') {
                $error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadOk = 0;
            }

            if ($uploadOk == 0) {
                set_message('Error uploading file: ' . $error);
            } else {
                $sql = "UPDATE users SET profile_image='$target_file' where id=$user_id";
                confrm(query($sql));

                set_message('Profile Image uploaded');

                if (!move_uploaded_file($_FILES["profile_image_file"]["tmp_name"], $target_file)) {
                    set_message('Error uploading file: ' . $error);
                }
            }
            redirect(location: 'profile.php');
        }
    }


    function user_restrictions()
    {
        if (!isset($_SESSION['email'])) {
            redirect(location: 'login.php');
        }
    }


    function login_check_pages()
    {
        if (isset($_SESSION['email'])) {
            redirect(location: 'index.php');
        }
    }


    // function  redirect($location)
    // {
    //     header(header: "location: {$location}");
    //     exit();
    // }

    function create_post()
    {
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] == "POST") {

            $post_content = clean($_POST['post_content']);

            if (strlen($post_content) > 200) {
                $errors[] = 'Your post content is too long!';
            }

            if (!empty($errors)) {
                foreach ($errors as $error) {
                    echo '<div class="alert">' . $error . '</div>';
                }
            } else {
                $post_content = filter_var($post_content, filter: FILTER_SANITIZE_STRING);
                $post_content = escape($post_content);
                $user = getUser();
                $user_id = $user['id'];


                $sql = "INSERT INTO posts(user_id, content, likes)";
                $sql .= "VALUES($user_id, '$post_content',0)";

                confrm(query($sql));

                set_message('You added a post!');
                redirect(location: 'index.php');
            }
        }
    }


    function fetch_all_posts()
    {
        $query = "SELECT * FROM posts ORDER BY created_time DESC";
        $result = query($query);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $user = getUser($row['user_id']);

                echo "<div class='post'><p><img src='" . $user['profile_image'] . "' alt=''><i><b>" . $user['first_name'] . " " . $user['last_name'] . "</b></i></p>
                        <p>" . $row['content'] . "</p>
                        <p><i>Date: <b>" . $row['created_time'] . "</b></i></p>
                        <div class='likes'>Likes: <b id='likes_" . $row['id'] . "'>" . $row['likes'] . "</b><button onclick='like_post(this)' data-post_id='" . $row['id'] . "'>LIKE</button></div></div>";
            }
        }
    }
