<?php

// secure the user input
function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function validateContact()
{
    // initate the variables 
    $name = $email = $phone = $salutation = $contactOption = $message = '';
    $nameErr = $emailErr = $phoneErr = $contactOptionErr = $messageErr = '';
    $valid = false;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // validate the 'POST' data
        if (empty($_POST["name"])) {
            $nameErr = "Name is required";
        } else {
            $name = test_input($_POST["name"]);
            if (!preg_match("/^[a-zA-Z-' ]*$/", $name)) {
                $nameErr = "Only letters and white space allowed";
            }
        }


        if (empty($_POST["email"])) {
            $emailErr = "Email is required";
        } else {
            $email = test_input($_POST["email"]);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emailErr = "Invalid email format";
            }
        }
        if (empty($_POST["phone"])) {
            $phoneErr = "Phone is required";
        } else {
            $phone = test_input($_POST["phone"]);
        }
        if (empty($_POST["salutation"])) {
            $salutation = "";
        } else {
            $salutation = test_input($_POST["salutation"]);
        }

        if (!empty($_POST["contactOption"]) && isset($contactOption)) {
            $contactOption = test_input($_POST["contactOption"]);
        } else {
            $contactOptionErr = "Contact option is required";
        }
        if (empty($_POST["message"])) {
            $messageErr = "Message is required";
        } else {
            $message = test_input($_POST["message"]);
        }

        if (strcmp($nameErr, '') == 0 && strcmp($emailErr, '') == 0 && strcmp($phoneErr, '') == 0 && strcmp($contactOptionErr, '') == 0 && strcmp($messageErr, '') == 0) {
            $valid = true;
        };
    }

    return array("salutation" => $salutation, "name" => $name, "email" => $email, "phone" => $phone, "contactOption" => $contactOption, "message" => $message, "nameErr" => $nameErr, "emailErr" => $emailErr, "phoneErr" => $phoneErr, "contactOptionErr" => $contactOptionErr, "messageErr" => $messageErr, "valid" => $valid);
}

function validateRegistration()
{
    // initate the variables 
    $name = $email = $password = $confirmPassword = '';
    $nameErr = $emailErr = $passwordErr = $confirmPasswordErr = '';
    $valid = false;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // validate the 'POST' data

        if (empty($_POST["name"])) {
            $nameErr = "Name is required";
        } else {
            $name = test_input($_POST["name"]);
            if (!preg_match("/^[a-zA-Z-' ]*$/", $name)) {
                $nameErr = "Only letters and white space allowed";
            }
        }

        if (empty($_POST["email"])) {
            $emailErr = "Email is required";
        } else {
            $email = test_input($_POST["email"]);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emailErr = "Invalid email format";
            }
        }
        if (empty($_POST["password"])) {
            $passwordErr = "Password is required";
        } else {
            $password = test_input($_POST["password"]);
        }

        if (empty($_POST["confirmPassword"])) {
            $confirmPasswordErr = "Please repeat your password";
        } else {
            $confirmPassword = test_input($_POST["confirmPassword"]);
            if (strcmp($confirmPassword, $password) != 0) {
                $confirmPasswordErr = "Passwords does not match";
            }
        }

        // Check if email is already in use, if not: create new user

        if ($name !== "" && $email !== "" && $password !== "" && $confirmPassword !== "" && $nameErr === "" && $emailErr === "" && $passwordErr === "" && $confirmPasswordErr === "") {
            $users_file = fopen("./users/users.txt", "r");
            while (!feof($users_file)) {
                $user = fgets($users_file);
                if (stripos(
                    $user,
                    $email
                ) !== false) {
                    $emailErr = "An account with this email is already in use";
                }
            }
            fclose($users_file);

            if ($emailErr === "") {
                $valid = true;

                $users_file = fopen("./users/users.txt", "a");
                $new_user = "$email|$name|$password\n";
                fwrite(
                    $users_file,
                    $new_user
                );
                fclose($users_file);
                header("location: index.php?page=login");
            }
        }
    }

    return array("name" => $name, "email" => $email, "password" => $password, "confirmPassword" => $confirmPassword, "nameErr" => $nameErr, "emailErr" => $emailErr, "passwordErr" => $passwordErr, "confirmPasswordErr" => $confirmPasswordErr, "valid" => $valid);
}

function validateLogin()
{

    // initiate the variables 
    $name = $email = $password = '';
    $emailErr = $passwordErr = '';
    $valid = false;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // validate the 'POST' data      

        if (empty($_POST["email"])) {
            $emailErr = "Email is required";
        } else {
            $email = test_input($_POST["email"]);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emailErr = "Invalid email format";
            }
        }
        if (empty($_POST["password"])) {
            $passwordErr = "Password is required";
        } else {
            $password = test_input($_POST["password"]);
        }

        // check if all data are valid
        // set username of logged in user
        if ($email !== "" && $password !== "" && $emailErr === "" && $passwordErr === "") {
            $users_file = fopen("./users/users.txt", "r");
            while (!feof($users_file)) {
                $user = fgets($users_file);
                $user_data = explode("|", $user);
                if ($email === $user_data[0] && $password === trim($user_data[2])) {
                    $emailErr = "";
                    $passwordErr = "";
                    $name = $user_data[1];
                    $valid = true;
                    break;
                } else {
                    $emailErr = "Email not found or password incorrect";
                }
            }
            fclose($users_file);
        }
    }

    // returning the data
    return array("name" => $name, "email" => $email, "password" => $password, "emailErr" => $emailErr, "passwordErr" => $passwordErr,  "valid" => $valid);
}
