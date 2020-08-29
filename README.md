<p align="center">
  <a href="https://www.gatsbyjs.org">
    <img alt="Noah" src="https://noahvarghese.me/favicon.ico" width="60" />
  </a>
</p>
<h1 align="center">
  userApi
</h1>

## 🚀 Quick start

1.  **Clone respoitory**

  ```shell
  # clone to webserver, and path it will be accessed from
  git clone https://github.com/noahvarghese/useApi
  ```
  
1.  **Update packages**

  ```shell
  composer install
  ```
 
1.  **Configure your own datbase**

  in config/database.php
  enter your own setup
 
1.  **Methods documented in user.php**

  - POST
  
    - Create User:
      - create: {boolean}*
      - firstName {string}*
      - lastName {string}*
      - email {string}*
      - password {string}*
      - confirmPssword {string}*
      
    - Login User
      - read: {boolean}*
      - loginEmail {string}*
      - loginPassword {string}*
      - Returns JWT 
   
  - PUT
    - Update User:
      - JWT in header *
      - firstName {string}
      - lastName {string}
      - email {string}
      - password {string}
      - confirmPassword {string}
      - Returns JWT
  - DELETE
    - Delete USer:
      - JWT in header *
      - password {string}*
