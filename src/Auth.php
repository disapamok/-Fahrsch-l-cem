<?php
namespace Simcify;

use Simcify\Str;

class Auth {
    
    /**
     * Authenicate a user
     * 
     * @param   \Std    $user
     * @param   boolean $remember
     * @return  void
     */
    public static function authenticate($user, $remember = false) {
        session(config('auth.session'), $user->id);
        if( $remember && isset($user->{config('auth.remember')}) ) {
            cookie('cmVtZW1iZXI', $user->{config('auth.remember')}, 30);
        }        
    }

    
    /**
     * Check if the user is authenticated
     * 
     * @return  void
     */
    public static function check() {
        return session()->has(config('auth.session'));
    }
    
    /**
     * Log out the authenticated user
     * 
     * @return  void
     */
    public static function deauthenticate() {
        if(isset($_COOKIE['cmVtZW1iZXI'])) {
            cookie('cmVtZW1iZXI', '', -7);
        }
        session()->flush();
    }

    /**
     * Create a valid password
     * 
     * @param   string  $string
     * @return  string
     */
    public static function password($str) {
        return hash_hmac('sha256', $str, config('auth.secret'));
    }

    /**
     * Remember a user
     * 
     * @return  void
     */
    public static function remember() {
        if ( !static::check() && !is_null(cookie('cmVtZW1iZXI')) ) {
            $remember_token = cookie('cmVtZW1iZXI');
            $user = Database::table(config('auth.table'))->where(config('auth.remember'), $remember_token)->first();
            if ( is_object($users) ) {
                static::authenticate($user);
            } else {
                static::deauthenticate();
            }
        }
    }

    
    /**
     * Get the authenticated user
     * 
     * @return \Std
     */
    public static function user() {
        return Database::table(config('auth.table'))->find(session(config('auth.session')) + 0);
    }
    
    /**
     * Login a user
     * 
     * @param string $username
     * @param password $password
     * @param string $options
     * @return mixed
     */
    public static function login($username, $password, $options = array()) {
        $givenPassword = self::password($password);
        $user = Database::table(config('auth.table'))->where(config('auth.emailColumn'), $username)->first();
        if($user == null){
            $user = Database::table(config('auth.table'))->where("username", $username)->first();            
        }

        if (!empty($user)) {
            if (isset($options["status"])) {
                $statusColumnName = config('auth.statusColumn');
                if ($options["status"] != $user->$statusColumnName) {
                    return array(
                        "status" => "error",
                        "title" => sch_translate("account_inactive"),
                        "message" => sch_translate("your_account_is_not_active")
                    );
                }
            }

            $passwordColumn = config('auth.passwordColumn');
            if(hash_compare($user->$passwordColumn, self::password($password))){
                if (isset($options["rememberme"]) && $options["rememberme"]) {
                    self::authenticate($user, true);
                }else{
                    self::authenticate($user);
                }

                if (isset($options['redirect'])) {
                    $response = array(
                        "status" => "success",
                        "notify" => false,
                        "callback" => "redirect('".$options['redirect']."', true);"
                    );
                }else{
                    $response = array(
                        "status" => "success",
                        "title" => sch_translate("login_successful"),
                        "message" => sch_translate("you_have_been_logged_in_successfully")
                    );
                }
                
            }else{
                $response = array(
                    "status" => "error",
                    "title" => sch_translate("incorrect_credentials"),
                    "message" => sch_translate("incorrect_username_or_password")
                );
            }
        }else{
            $response = array(
                "status" => "error",
                "title" => sch_translate("user_not_found"),
                "message" => sch_translate("incorrect_username_or_password")
            );
        }

        return $response;
    }
    
    /**
     * Sign up new user
     * 
     * @param array $data
     * @param array $options
     * @return mixed
     */
    public static function signup($data, $options = array()) {

        if (isset($options['uniqueEmail'])) {
            $user = Database::table(config('auth.table'))->where(config('auth.emailColumn'),$options["uniqueEmail"])->first();
            if (!empty($user) || !empty($username_user)) {
                return array(
                    "status" => "error",
                    "title" => sch_translate("email_already_exists"),
                    "message" => sch_translate("email_already_exists")
                );
            }
        }

        $insert = Database::table(config('auth.table'))->insert($data);

        $newUserId = Database::table(config('auth.table'))->insertId();

        if (isset($options["authenticate"]) AND $options["authenticate"]) {
            $user = Database::table(config('auth.table'))->where("id",$newUserId)->first();
            self::authenticate($user);
        }

        if (isset($options['redirect'])) {
            $response = array(
                "status" => "success",
                "notify" => false,
                "callback" => "redirect('".$options['redirect']."', true);"
            );
        }else{
            $response = array(
                "status" => "success",
                "title" => sch_translate("sign_up_successful"),
                "message" => sch_translate("your_account_was_created_successfully"),
                "id" => $newUserId
            );
        }

        return $response;
    }
    
    /**
     * forgot password
     * 
     * @param string $email
     * @param string $resetlink
     * @return mixed
     */
    public static function forgot($email, $resetlink) {
        $user = Database::table(config('auth.table'))->where(config('auth.emailColumn'),$email)->first();
        if (!empty($user)) {

            $token = Str::random(32);
            $data = array(config('auth.passwordTokenColumn') => $token);
            $update = Database::table(config('auth.table'))->where(config('auth.emailColumn') ,$email)->update($data);
            $resetLink = str_replace("[token]", $token, $resetlink);

            $send = Mail::send(
                $email,
                env("APP_NAME").sch_translate("password_reset"),
                array(
                    "title" => sch_translate("password_reset"),
                    "subtitle" => sch_translate("click_the_the_button_below_to_reset_your_password"),
                    "buttonText" => sch_translate("reset_password"),
                    "buttonLink" => $resetLink,
                    "message" => sch_translate("someone_hopefully_you_has_requested_a_password_reset_on_your_account_if_it_is_you_go_ahead_and_reset_your_password_if_not_please_ignore_this_email")),
                "withbutton"
            );

            if ($send) {
                    $response = array(
                        "status" => "success",
                        "title" => sch_translate("email_sent"),
                        "message" => sch_translate("email_with_reset_instructions_successfully_sent"),
                        "callback" => "redirect('".url("Auth@get")."')"
                    );
            }else{
                    $response = array(
                        "status" => "error",
                        "title" => sch_translate("failed_to_reset"),
                        "message" => $send->ErrorInfo
                    );
            }
        }else{
            $response = array(
                "status" => "error",
                "title" => sch_translate("account_not_found"),
                "message" => sch_translate("account_with_this_email_was_not_found")
            );
        }

        return $response;

    }
    
    /**
     * reset password
     * 
     * @param string $token
     * @param string $password
     * @return mixed
     */
    public static function reset($token, $password) {
        $user = Database::table(config('auth.table'))->where(config('auth.passwordTokenColumn'),$token)->first();
        if (!empty($user)) {
            $data = array(config('auth.passwordTokenColumn') => "" , config('auth.passwordColumn') => self::password($password));
            $update = Database::table(config('auth.table'))->where("id",$user->id)->update($data);

            if ($update) {
                    $response = array(
                        "status" => "success",
                        "title" => sch_translate("password_reset"),
                        "message" => sch_translate("password_successfully_reset"),
                        "callback" => "redirect('".url("Auth@get")."', true);"
                    );
            }else{
                    $response = array(
                        "status" => "error",
                        "title" => sch_translate("failed_to_reset"),
                        "message" => sch_translate("failed_to_reset_password_please_try_again")
                    );
            }
        }else{
            $response = array(
                "status" => "error",
                "title" => sch_translate("token_mismatch"),
                "message" => sch_translate("token_not_found_or_expired")
            );
        }

        return $response;

    }
}
