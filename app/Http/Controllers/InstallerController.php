<?php

namespace App\Http\Controllers;

use App\Installer\Installer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\In;

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
    public function checkDatabase(Request $request) :Response {
        try {
            if(Installer::db()->isDbEmpty()){
                return response()->json(['success' => true, 'message' => 'Database is empty and ready to start migration process']);
            }
            return response()->json(['hasErrors' => true, 'has_tables' => true, 'tables' => Installer::db()->getDatabaseTables()]);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['hasErrors' => true, 'message' => "There was an error while checking database for tables, please check error logs"], 500);
        }
    }
    public function dropTables(Request $request) :Response {
        try {
            if(!Installer::db()->isDbEmpty()) {
                Installer::db()->dropTables();
                return response()->json(['success' => true, 'message' => 'successfully emptied the database!']);
            }
            return response()->json(['hasErrors' => true, 'message' => 'No tables to drop!']);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['hasErrors' => true, 'message' => 'There were some errors when trying to drop database tables, please check error logs'], 500);
        }
    }
    public function createTables(Request $request) :Response {
        try {
            if(Installer::db()->isDbEmpty()){
                Installer::db()->createDBTables();
                // Let's now re-check if there are some missing database tables
                if(!empty(Installer::db()->hasMissingTables())){
                    return response()->json(['success' => false, 'message' => 'Some tables were not successfully created', 'tables' => Installer::db()->hasMissingTables()]);
                }
                return response()->json(['success' => true, 'message' => 'Successfully created database tables!']);
            }
            return response()->json(['hasErrors' => true, 'message' => "Can't create database tables, the database is not empty!"]);
        } catch(\Exception $e) {
            Log::error($e);
            return response()->json(['hasErrors' => true, 'message' => "There were some errors when trying to create database tables, please check error logs"], 500);
        }
    }
    public function createDefaultGroups(Request $request) :Response {
        try {
            if(!Installer::db()->isDbEmpty() && empty(Installer::db()->hasMissingTables()) && !Installer::db()::seeder()->hasUserGroup()
            && !Installer::db()::seeder()->hasAdminGroup()) {
                Installer::db()::seeder()->createUserGroup();
                Installer::db()::seeder()->createAdminGroup();
                return response()->json(['success' => true, 'message' => 'Successfully created default user groups']);
            }
            return response()->json(['hasErrors' => true, 'message' => 'Default user groups already exists!']);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['hasErrors' => true, 'message' => "There were some errors when trying to create default user groups, please check error logs"], 500);
        }
    }
}
