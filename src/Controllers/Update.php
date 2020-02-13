<?php
namespace Simcify\Controllers;

use PDO;
use Simcify\Auth;
use Simcify\Database;
use DotEnvWriter\DotEnvWriter;

class Update {

    /**
     * Get settings view
     * 
     * @return \Pecee\Http\Response
     */
    public function get() {
        $user = Auth::user();
        if ($user->role != "superadmin") {
            return view('errors/404');   
        }
        $versions = self::versions(); 
        $currentVersion = env("APP_VERSION");
        $latest = $versions[$currentVersion];
        return view('update', compact("user","latest"));
    }

    /**
     * Get settings view
     * 
     * @return Json
     */
    public function scan() {
        header('Content-type: application/json');
        $currentVersion = env("APP_VERSION");
        $versions = self::versions(); 
        $updateTo = $versions[$currentVersion];
        if (is_null($updateTo)) {
            exit(json_encode(responder("warning", sch_translate("hmm"), sch_translate("you_are_running_on_the_latest_version_with_vid", [$updateTo]))));
        }
        self::update($updateTo);
        exit(json_encode(responder("success", sch_translate("complete")."!", sch_translate("updated_successfully_completed"),"reload()")));
    }

    /**
     * App versions
     * 
     * @return Array
     */
    public function versions() {
        return array(
                    "1.0" => "2.0",
                    "2.0" => NULL
                    );
    }

    /**
     * Update Signer
     * 
     * @return Json
     */
    public function update($version) {
        $envPath = str_replace("src/Controllers", ".env", dirname(__FILE__));
        $env = new DotEnvWriter($envPath);
        $env->castBooleans();
        $versionUpdates = file_get_contents(config("app.storage")."updates/".$version.".json");
        $updates = explode("@signer", $versionUpdates);

        foreach ($updates as $update) {
            Database::table("users")->command($update);
        }
        
        $env->set("APP_VERSION", $version);
        $env->save();
    }


}
