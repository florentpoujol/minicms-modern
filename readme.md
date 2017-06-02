# Mini CMS - MVC

The point of this project is to practice web development, specifically back-end dev with PHP, by creating a basic CMS, using as much OOP as possible (where it makes sens) and notably the MVC design pattern, but without using any frameworks or non-native libraries.

Exceptions for non-native libraries: Markdown, PHP Mailer

## General features

### Users

- 3 roles: admin, writer, commenter
- registering of new users via a public form or by admins
  - emails of new users must be validated via a link sent to their address
  - registering can be turned off globally
- Standard login via username and password
  - forgot password function that sends an email to the user allowing him to access the form to reset the password within 48h
- users can edit their infos, admins can edit everyones
- users can't delete themselves
- admin can ban users
- deleting a user deletes all its comments, reaffects its resources to the user that deleted it

### Medias

- upload and deletion of media (images, zip, pdf)

### Posts and categories

- standard posts linked to categories
- content is markdown
- only created by admin or writers
- can have comments (comments can be turned of on a per-post basis)
- the blog page show the X last posts
- categories page show the post of that category

### Pages

- content is markdown
- only created by admin or writers
- can have comments (comments can be turned of on a per-page basis)
- can be children of another page (if it isn't itself a child)

### Comments

- comments can be added by any registered users on pages and posts where it's allowed
- comments can be turned off globally
- users can edit their comments or delete them in the admin section
- writer can see all comments of their pages and posts (admins can see alls)

## Miscellaneous

- secure forms, requests to database and display of data
- full validation of data on the backend side (writers or commenters can't do anything they aren't supposed to do, even when modifying the HTML of a form through the browser's dev tools)
- nice handling of all possible kinds of errors and success messages
- emails can be sent via the local email software or SMTP
- global configuration saved as JSON can be editted via the file or by admins via a form
- must work with PHP7/5.6 MySQL5.6+ not use any deprecated stuff
- works with or without URL rewriting, with .htaccess provided
- works as a subfolder or the root of a domain name
- links to pages, posts, categories and medias can be added in the pages or posts content via shortcodes
- works with or without SSL. All internals links adapt automatically to the protocol used (+ url rewrite or not).
- optionnal use of recaptcha on all public forms (set via the secret key in config)
- Once completed a backup of the database must be supplied with structure and some actual content
- easy install via a script once put up on an FTP
