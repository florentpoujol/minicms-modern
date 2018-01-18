# Mini CMS - Modern

The point of this project is to practice back-end web developement with PHP, by creating a basic CMS with __modern__ practices:
- OOP and ideally no strictly procedural code (ie: no global helper functions),
- MVC, plus the single `index.php` front controller and all other `.php` files outside of the web root ,
- use some of the [PHP Standard Components](https://github.com/florentpoujol/PHP-Standard-Components) (that I also created myself),
- no framework or non-native libraries (except for my Standard Components of course, as well as Markdown, PHP Mailer and PHPUnit)

_See also [MINI CMS - Old-School - Vanilla](https://github.com/florentpoujol/minicms-osv), the same kind of project but completely procedural, without specific organisation of files and no libraries._

## General features

### Users

- 3 roles: admin, writer, commenter
- registering of new users via a public form or by admins
  - emails of new users must be validated via a link sent to their address
  - registering can be turned off globally
- Standard login via username and password
  - forgot password function that sends an email to the user allowing him to access the form to reset the password within 48h
- commenters can only edit their profile
- writers can see all existing users and edit their profile
- admins can see/edit/delete all users
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
- the blog page show the last posts with a list of the categories in a sidebar

### Pages

- content is markdown
- only created by admin or writers
- can have comments (comments can be turned of on a per-page basis)
- can be children of another page (if it isn't itself a child)

### Comments

- comments can be added by any registered users on pages and posts where it's allowed
- comments can be turned off globally or on a per-page/post basis
- users can edit their comments in the admin section
- writer can also see and edit the comments attached to their pages and posts 
- admins can see/update/delete all comments

## Miscellaneous

- secure forms, requests to database and display of data
- full validation of data on the backend side (writers or commenters can't do anything they aren't supposed to do, even when modifying the HTML of a form through the browser's dev tools)
- nice handling of all possible kinds of errors and success messages
- emails can be sent via the local email software or SMTP
- global configuration saved as JSON can be editted via the file or by admins via a form
- must work with PHP7.0+ MySQL5.6+ and not use any deprecated stuff
- works with or without URL rewriting, with .htaccess provided
- works as a subfolder or the root of a domain name
- links to pages, posts, categories and medias can be added in the content via wordpress-like shortcodes. Ie: [link:mdedia:the-media-slug]
- works with or without SSL. All internals links adapt automatically to the protocol used (+ url rewrite or not).
- optional use of Recaptcha on all public forms (set via the secret key in config)
- easy install via a script once put up on an FTP
