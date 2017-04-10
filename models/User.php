<?php

class User{
    private $user_pseudo;
    private $user_password;
    private $user_fisrtName;
    private $user_lastName;
    private $user_mail;
    private $user_country;

    public function __construct($user_pseudo, $user_password, $user_fisrtName, $user_lastName, $user_mail, $user_country) {
      $this->user_pseudo = $user_pseudo;
      $this->user_password = $user_password;
      $this->user_fisrtName = $user_fisrtName;
      $this->user_lastName = $user_lastName;
      $this->user_mail = $user_mail;
      $this->user_country = $user_country;
     }

    public function setUser_pseudo($user_pseudo) {
           $this->user_pseudo = $user_pseudo;
       }
     public function getUser_pseudo() {
         return $this->user_pseudo;
     }

     public function setUser_password($user_password) {
            $this->user_password = $user_password;
        }
      public function getUser_password() {
          return $this->user_password;
      }


      public function setUser_fisrtName($user_fisrtName) {
             $this->user_fisrtName = $user_fisrtName;
         }
       public function getUser_fisrtName() {
           return $this->user_fisrtName;
       }

       public function setUser_lastName($user_lastName) {
              $this->user_lastName = $user_lastName;
          }
        public function getUser_lastName() {
            return $this->user_lastName;
        }

        public function setUser_mail($user_mail) {
               $this->user_mail = $user_mail;
           }
         public function getUser_mail() {
             return $this->user_mail;
         }

         public function setUser_country($user_country) {
                $this->user_country = $user_country;
            }
          public function getUser_country() {
              return $this->user_country;
          }

          public function setUser_created_at($user_created_at) {
                 $this->user_created_at = $user_created_at;
             }
           public function getUser_created_at() {
               return $this->user_created_at;
           }

           public function setUser_type($user_type) {
                  $this->user_type = $user_type;
              }
            public function getUser_type() {
                return $this->user_type;
            }
}
