<?php

class User{
    public $user_pseudo;
    public $user_password;
    public $user_fisrtName;
    public $user_lastName;
    public $user_mail;
    public $user_country;

    public function __construct($user_pseudo, $user_password, $user_fisrtName, $user_lastName, $user_mail, $user_country) {
      $this->user_pseudo = $user_pseudo;
      $this->user_password = $user_password;
      $this->user_fisrtName = $user_fisrtName;
      $this->user_lastName = $user_lastName;
      $this->user_mail = $user_mail;
      $this->user_country = $user_country;
     }

    public function setUser_pseudo($user_pseudo) {
           $this->pseudo = $user_pseudo;
       }
     public function getUser_pseudo() {
         return $this->pseudo;
     }

     public function setUser_password($user_password) {
            $this->password = $user_password;
        }
      public function getUser_password() {
          return $this->password;
      }


      public function setUser_fisrtName($user_fisrtName) {
             $this->fisrtName = $user_fisrtName;
         }
       public function getUser_fisrtName() {
           return $this->fisrtName;
       }

       public function setUser_lastName($user_lastName) {
              $this->lastName = $user_lastName;
          }
        public function getUser_lastName() {
            return $this->lastName;
        }

        public function setUser_mail($user_mail) {
               $this->mail = $user_mail;
           }
         public function getUser_mail() {
             return $this->mail;
         }

         public function setUser_country($user_country) {
                $this->country = $user_country;
            }
          public function getUser_country() {
              return $this->country;
          }

          public function setUser_created_at($user_created_at) {
                 $this->created_at = $user_created_at;
             }
           public function getUser_created_at() {
               return $this->created_at;
           }

           public function setUser_type($user_type) {
                  $this->type = $user_type;
              }
            public function getUser_type() {
                return $this->type;
            }
}
