<?php

namespace App\Http\Controllers;

use App\Installer\Installer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class InstallerController extends Controller
{
    public function envSettings(Request $request) :Response
    {
        try {
            return response()->json([
                'db_host_empty' => Installer::env()->isDBHostEmpty(),
                'db_user_empty' => Installer::env()->isDBUsernameEmpty(),
                'db_pass_empty' => Installer::env()->isDBPassEmpty(),
                'db_name_empty' => Installer::env()->isDBNameEmpty(),
                'db_port_empty' => Installer::env()->isDBPortEmpty()
            ]);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['hasErrors' => true, 'message' => 'Something went wrong, please check error logs'], 500);
        }
    }
    public function writeEnvSettings(Request $request) :Response {
        try {
            $data = $request->validate([
                'dbhost' => 'required',
                'dbuser' => 'required',
                'dbpass' => 'required',
                'dbport' => 'required|integer',
                'dbname' => 'required'
            ]);
            Installer::env()->setDbData($data['dbhost'],$data['dbuser'],$data['dbpass'],$data['dbport'],$data['dbname']);
            // Now let's check the connection
            if(Installer::db()->getConnection()){
                // We have successfully connected to the database
                return response()->json(['success' => true, 'message' => 'Successfully connected to the database.']);
            }
            return response()->json(['hasErrors' => true, 'message' => 'No connection could be made.']);
        } catch(\Exception $e) {
            Log::error($e);
            return response()->json(['hasErrors' => true, 'message' => 'Something went wrong, please check error logs'], 500);
        }
    }
    public function checkConnection(Request $request) :Response {
        try {
            if(Installer::db()->getConnection()) {
                return response()->json(['success' => true, 'message' => 'Successfully validated db connection']);
            }
            return response()->json(['hasErrors' => true, 'message' => 'No connection could be made with database!']);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['hasErrors' => true, 'message' => 'There is problem when trying to get connection with mysql. Check error logs'], 500);
        }
    }

}
