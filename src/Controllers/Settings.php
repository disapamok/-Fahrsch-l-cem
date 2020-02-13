<?php
namespace Simcify\Controllers;

use Simcify\Auth;
use Simcify\File;
use Simcify\Database;
use DotEnvWriter\DotEnvWriter;

class Settings {

    /**
     * Get settings view
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        $user      = Auth::user();
        $timezones = Database::table("timezones")->get();
        $currencies = Database::table("currencies")->get();
        $school    = Database::table("schools")->where("id", $user->school)->first();
        $reminders = Database::table("reminders")->where("school", $user->school)->get();
        return view('settings', compact("user", "school", "reminders","currencies","timezones"));
    }

    public function getCurrencySymbol($currencyCode, $locale = 'en_US')
{
    $formatter = new \NumberFormatter($locale . '@currency=' . $currencyCode, \NumberFormatter::CURRENCY);
    return $formatter->getSymbol(\NumberFormatter::CURRENCY_SYMBOL);
}

    /**
     * Update profile on settings page
     * 
     * @return Json
     */
    public function updateprofile() {
        $account = Database::table(config('auth.table'))->where("email", input("email"))->first();
        if (!empty($account) && $account->id != Auth::user()->id) {
            exit(json_encode(responder("error", sch_translate("oops"), sch_translate("already_exist_with_text", [input("email")]) )));
        }
        foreach (input()->post as $field) {
            if ($field->index == "avatar") {
                if (!empty($field->value)) {
                    $avatar = File::upload($field->value, "avatar", array(
                        "source" => "base64",
                        "extension" => "png"
                    ));
                    if ($avatar['status'] == "success") {
                        if (!empty(Auth::user()->avatar)) {
                            File::delete(Auth::user()->avatar, "avatar");
                        }
                        Database::table(config('auth.table'))->where("id", Auth::user()->id)->update(array(
                            "avatar" => $avatar['info']['name']
                        ));
                    }
                }
                continue;
            }
            if ($field->index == "csrf-token") {
                continue;
            }
            if ($field->index == "date_of_birth") {
                $field->value = date('Y-m-d', strtotime($field->value));
                Database::table(config('auth.table'))->where("id", Auth::user()->id)->update(array(
                    $field->index => escape($field->value)
                ));
                continue;
            }
            Database::table(config('auth.table'))->where("id", Auth::user()->id)->update(array(
                $field->index => escape($field->value)
            ));
        }
        return response()->json(responder("success", sch_translate("alright"), sch_translate("profile_successfully_updated")));
    }

    /**
     * Update School on settings page
     * 
     * @return Json
     */
    public function updatecompany() {
        foreach (input()->post as $field) {
            if ($field->index == "csrf-token") {
                continue;
            }
            Database::table("schools")->where("id", Auth::user()->school)->update(array(
                $field->index => escape($field->value)
            ));
        }
        return response()->json(responder("success", sch_translate("alright"), sch_translate("school_info_successfully_updated")));
    }

    /**
     * Update reminders on settings page
     * 
     * @return Json
     */
    public function updatereminders() {
        $user = Auth::user();
        if (empty(input("payment_reminders"))) {
            Database::table("schools")->where("id", $user->school)->update(array(
                "payment_reminders" => "Off"
            ));
        } else {
            Database::table("schools")->where("id", $user->school)->update(array(
                "payment_reminders" => "On"
            ));
        }
        if (empty(input("class_reminders"))) {
            Database::table("schools")->where("id", $user->school)->update(array(
                "class_reminders" => "Off"
            ));
        } else {
            Database::table("schools")->where("id", $user->school)->update(array(
                "class_reminders" => "On"
            ));
        }
        Database::table("reminders")->where("school", $user->school)->delete();
        foreach (input("message") as $index => $message) {
            $reminder = array(
                "school" => $user->school,
                "days" => input("days")[$index]->value,
                "type" => input("type")[$index]->value,
                "timing" => input("timing")[$index]->value,
                "send_via" => input("send_via")[$index]->value,
                "subject" => escape(input("subject")[$index]->value),
                "message" => escape(input("message")[$index]->value)
            );
            Database::table("reminders")->insert($reminder);
        }
        return response()->json(responder("success", sch_translate("alright"), sch_translate("reminders_successfully_updated")));
    }

    /**
     * Update password on settings page
     * 
     * @return Json
     */
    public function updatepassword() {
        $user = Auth::user();
        if (hash_compare($user->password, Auth::password(input("current")))) {
            Database::table(config('auth.table'))->where("id", $user->id)->update(array(
                "password" => Auth::password(input("password"))
            ));
            return response()->json(responder("success", sch_translate("alright"), sch_translate("password_successfully_updated"), "reload()"));
        } else {
            return response()->json(responder("error", sch_translate("oops"), sch_translate("you_have_entered_an_incorrect_password")));
        }
    }

    /**
     * Update system settings
     * 
     * @return Json
     */
    public function updatesystem() {
        $envPath = str_replace("src/Controllers", ".env", dirname(__FILE__));
        $env     = new DotEnvWriter($envPath);
        $env->castBooleans();
        $enableToggle = array(
            "ALLOW_SIGNUP",
            "SHOW_SCHOOLS_MENU"
        );
        foreach ($enableToggle as $key) {
            if (empty(input($key))) {
                $env->set($key, 'Disabled');
            }
        }
        if (empty(input("SMTP_AUTH"))) {
            $env->set("SMTP_AUTH", false);
        }
        $env->set("MAIL_SENDER", input("APP_NAME") . " <" . input("MAIL_USERNAME") . ">");
        foreach (input()->post as $field) {
            if ($field->index == "APP_LOGO") {
                if (!empty($field->value)) {
                    $upload = File::upload($field->value, "app", array(
                        "source" => "base64",
                        "extension" => "png"
                    ));
                    if ($upload['status'] == "success") {
                        File::delete(env("APP_LOGO"), "app");
                        $env->set("APP_LOGO", $upload['info']['name']);
                        $env->save();
                    }
                }
                continue;
            }
            if ($field->index == "APP_ICON") {
                if (!empty($field->value)) {
                    $upload = File::upload($field->value, "app", array(
                        "source" => "base64",
                        "extension" => "png"
                    ));
                    if ($upload['status'] == "success") {
                        File::delete(env("APP_ICON"), "app");
                        $env->set("APP_ICON", $upload['info']['name']);
                        $env->save();
                    }
                }
                continue;
            }
            if ($field->index == "csrf-token") {
                continue;
            }
            $env->set($field->index, $field->value);
            $env->save();
        }
        return response()->json(responder("success", sch_translate("alright"), sch_translate("system_settings_successfully_updated"), "reload()"));
    }
}