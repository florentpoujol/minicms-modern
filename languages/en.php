<?php
// english language
return [

    "login" => [
        "pagetitle" => "Login",
    ],
    "lostpassword" => [
        "pagetitle" => "Lost password"
    ],
    "register" => [
        "pagetitle" => "Register",
    ],

    "user" => [
        ""
    ],

    "messages" => [
        "success" => [
            "email" => [
                "changepassword" => "Email sent, click the link within 48h"
            ],
        ],
        "error" => [
            "csrffail" => "CSRF validation has failed",
            "user" => [
                "alreadyloggedin" => "You are already logged in",
                "unknow" => "Unknow user",
                "loggedin" => "Welcome {username}, you are now logged in",
                "wrongpassword" => "Wrong password",
                "notactivated" => "This user's email address is not confirmed yet. You can sent the activation email again, blabla",
                "unauthorized" => "You are not authorized to access this page"
            ],
            "fieldvalidation" => [
                "email" => "The email has the wrong format",
                "name" => "The name has the wrong format",
                "title" => "The title has the wrong format",
                "password" => "The password has the wrong format",
                "passwordnotequal" => "The password has the wrong format, or the passwords do not matches"
            ],
            "db" => [
                "resetpassword" => "There has been an error, password was not changed."
            ]
        ],
    ]
];
