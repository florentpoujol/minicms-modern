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
    "resendconfirmemail" => [
        "pagetitle" => "Rensend confirmation email"
    ],

    "user" => [
        ""
    ],

    "formlabel" => [
        "name" => "Name: ",
        "title" => "Title: ",
        "slug" => "Slug: ",
        "email" => "Email: ",
        "password" => "Password: ",
        "password_confirm" => "Password confirmation: ",
    ],

    "email" => [
        "confirmemail" => [
            "subject" => "Confirm your email address",
            "body" => "You have registered or changed your email address on the site. <br> Please click the link below to verify the email adress. <br><br> <a href='{url}'>{url}</a>"
        ],
        "changepassword" => [
            "subject" => "Change your password",
            "body" => "You have requested to change your password. <br> Click the link below within 48 hours to access the form.<br><br> <a href='{url}'>{url}</a>"
        ],
    ],

    "messages" => [
        "success" => [
            "email" => [
                "changepassword" => "Email sent, click the link within 48h",
                "confirmemail" => "Email sent, click the link to confirm that you address exists",
            ],
            "user" => [
                "emailconfirmed" => "Email confirmed, please login",
                "created" => "user created"
            ]
        ],
        "error" => [
            "error" => "There has been an error, please try again",
            "csrffail" => "CSRF validation has failed",
            "user" => [
                "alreadyloggedin" => "You are already logged in",
                "unknow" => "Unknow user",
                "loggedin" => "Welcome {username}, you are now logged in",
                "wrongpassword" => "Wrong password",
                "notactivated" => "This user's email address is not confirmed yet. You can sent the activation email again, blabla",
                "alreaddyactivated" => "this email address is already confirmed. you can login with this user",
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
                "resetpassword" => "There has been an error, password was not changed.",
                "updateemailtoken" => "Error while updating email token",
                "createuser" => "Error creating the user."
            ],
            "email" => [
                "notsent" => "The email wasn't sent"
            ]
        ],
    ]
];
