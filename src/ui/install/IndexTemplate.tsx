import {memo, useState} from "react";
import {Card} from "@heroui/card";
import {Button} from "@heroui/button";
import {Input, Textarea} from "@heroui/input";
import React from "react";
import {DBConnection} from "@/Types/DBConnection";
import {DBTables} from "@/Types/DBTables";
import {Modal, ModalBody, ModalContent, ModalFooter, ModalHeader} from "@heroui/modal";
import {DBDropType} from "@/Types/DBDropType";
import {Form} from "@heroui/form";
import {AllowedTypes, DBFormData} from "@/Types/DBFormData";

/**
 * To do list:
 *  - Admin account creation
 *  - Website setting prompt (like website name, etc)
 *  - Complete the installation & Lock the install.
 * @constructor
 */


export default memo(function IndexTemplate()
{
    const [step, setStep] = useState(1);
    const [isLoading, setIsLoading] = useState(false);
    const [headingText, setHeading] = useState("Welcome to the BlogForge v1.0");
    const [isInstalling, setIsInstalling] = useState(false);
    const [installStatus, setInstallStatus] = useState<string[]>([]);
    const [isRemoving, setIsRemoving] = useState(false);
    const [dbTables, setDbTables] = useState<string[]>([]);
    const [dbData, setDbData] = useState<DBFormData>({
       dbhost:'',
       dbname:'',
       dbuser:'',
       dbpass:'',
       dbport:'3306'
    });

    type Connection = {
        data:DBConnection
    }
    type ResponseTableType = {
        data:DBTables
    }
    const onClickEvent = async () =>  {
        setIsLoading(true);
        setHeading("Checking DB Connection...");
        setInstallStatus((prev) => [...prev, "Checking database connection."]);
        setIsInstalling(true);
        const dbCheck = await fetch("/api/db/connection");
        const response :Connection  = await dbCheck.json();
        console.log(response);
        if(response.data.hasErrors) {
            setIsLoading(false);
            setHeading("Checking Environment DB settings...");
            setInstallStatus((prev) => [...prev, response.data.message]);
            setStep(2);
        }
        if(response.data.success){
            setStep(3);
            setInstallStatus((prev) => [...prev, "Skipping step environment checking, DB connection is successfully validated."]);
            setHeading("Checking Database for Tables...")
            const dbTables = await fetch("/api/db/tables");
            const response :ResponseTableType = await dbTables.json();
            if(response.data.hasErrors && response.data.has_tables && response.data.tables)
            {
                setInstallStatus((prev) => [...prev, "Database is not empty, prompting for removal..."]);
                const tables = response.data.tables;
                for(let i = 0; i < tables.length; i++ ){
                    setDbTables((prev) => [...prev, tables[i]]);
                }
            } else {
                await CreateTables();
            }
        }
    }
    const RemoveTables = async () => {
        setIsRemoving(true);
        const response = await fetch("/api/db/tables/drop");
        const data :DBDropType = await response.json();
        if(data.hasErrors) {
            setHeading("Installation aborted");
            setInstallStatus((prev) => [...prev, data.message]);
        }
        if(data.success){
            setIsRemoving(false);
            setInstallStatus((prev) => [...prev, data.message]);
            await CreateTables();
        }
    }
    const CreateTables = async () => {
        setStep(4);
        setHeading("Creating database tables...");
        setInstallStatus((prev) => [...prev, "Creating database tables..."]);
        const response = await fetch("/api/db/tables/create", {method:"POST"});
        const data :DBDropType = await response.json();
        if(data.hasErrors) {
            setHeading("Installation aborted!");
            setInstallStatus((prev) => [...prev, data.message]);
        }
        console.log(data);
        if(data.success){
            setStep(5);
            setHeading("Creating default user groups...");
            setInstallStatus((prev) => [...prev, "Creating default user groups..."]);
            const response = await fetch("/api/db/migrations/groups",{method:"POST"});
            const data :DBConnection = await response.json();
            if(data.hasErrors){
                setHeading("Installation aborted!");
                setInstallStatus((prev) => [...prev, data.message]);
            }
            if(data.success){
                setStep(6);
                setHeading("Admin Account");
                setInstallStatus((prev) => [...prev, "Creating admin account"]);
            }
        }
    }
    const ValidateAndSend = async () => {
        const form = new FormData();
        (Object.entries(dbData) as [AllowedTypes, string | number | null | undefined][]).forEach(
            ([key, value]) => {
                if (value != null) { // filters out null and undefined
                    form.set(key, String(value)); // safely convert to string
                }
            }
        );
        const response = await fetch("/api/db/environment/add",{
            method:"POST",
            body:form
        });
        const data :DBConnection = await response.json();
        if(data.hasErrors) {
            setHeading("Installation aborted");
            setInstallStatus((prev) => [...prev, data.message]);
        }
        if(data.success) {
            setInstallStatus((prev) => [...prev, data.message]);
            // lets check for table existence
            const dbTables = await fetch("/api/db/tables");
            const response :ResponseTableType = await dbTables.json();
            if(response.data.hasErrors && response.data.has_tables && response.data.tables)
            {
                setIsInstalling(true);
                setStep(3);
                setHeading("Database not empty.");
                setInstallStatus((prev) => [...prev, "Database is not empty, prompting for removal..."]);
                const tables = response.data.tables;
                for(let i = 0; i < tables.length; i++ ){
                    setDbTables((prev) => [...prev, tables[i]]);
                }
            } else {
                await CreateTables();
            }
        }
    }

    const ValidateTextInput   = (name:AllowedTypes, value:string) :true|null|undefined|string => {
        if(/^[a-zA-Z.0-9#!@%^&()_-]+$/.test(value)){
            setDbData((prev) => ({...prev, [name]:value}));
            return null;
        }
        return "Invalid characters";
    }
    const promptForDBDetails = isInstalling && step === 2 ? (
        <>
            <Modal isOpen={true}>
                <ModalContent>
                    <ModalHeader className="flex flex-col gap-1">Database Details</ModalHeader>
                    <ModalBody>
                        <Form>
                            <Input label="Database host"
                                   isRequired
                                   labelPlacement="inside"
                                   name="dbhost"
                                   description="By default the database host is localhost, if unsure please contact your hosting provider."
                                   validate={(value:string) => {
                                       if(value.length > 0){
                                           return null;
                                       } else {
                                           return "This field is required";
                                       }
                                   }}
                                   onChange={(e) => {
                                       const {value} = e.target;
                                       ValidateTextInput('dbhost', value);
                                   }}
                            />
                            <Input label="Database name"
                                   labelPlacement="inside"
                                   name="dbname"
                                   description = "Enter database name in the field above. Make sure it's valid."
                                   validate={(value:string) => {
                                       if(value.length > 0) {
                                           return null;
                                       } else {
                                           return "This field is required !";
                                       }
                                   }}
                                   onChange={(e) => {
                                       const {value} = e.target;
                                       ValidateTextInput('dbname', value);
                                   }}
                            />
                            <Input label="Database user"
                                   labelPlacement="inside"
                                   name="dbuser"
                                   description="Database user associated with the database name!"
                                   validate={(value:string) => {
                                       if(value.length > 0){
                                           return null;
                                       } else {
                                           return "This field is required";
                                       }
                                   }}
                                   onChange={(e) => {
                                       const {value} = e.target;
                                       ValidateTextInput("dbuser", value);
                                   }}
                            />
                            <Input label="Database password"
                                   labelPlacement="inside"
                                   name="dbpass"
                                   description="Database password for user" validate={(value) => {
                                       if(value.length > 0){
                                           return null;
                                       } else {
                                           return "This field is required";
                                       }
                            }}
                            onChange={(e) => {
                                const {value} = e.target;
                                ValidateTextInput("dbpass", value);
                            }}/>
                            <Input label="Database port" type="number"
                                   labelPlacement="inside"
                                   name='dbport'
                                   description="On most hosting providers the default port is 3306. If unsure, please contact your hosting provider for MySQL connection port."
                                   defaultValue="3306"
                                   validate={(value:string) => {
                                     if(value.length > 0){
                                         return null;
                                     } else {
                                         return "This field is required";
                                     }
                                   }}
                                   onChange={(e) => {
                                       const {value} = e.target;
                                       setDbData((prev) => ({...prev, dbport:value}));
                                   }}
                            />
                            <div className="flex w-[100%] flex-col flex-wrap space-y-3">
                                <Button isLoading={isLoading} className="bg-primary" onPress={ValidateAndSend}>
                                    Save Changes
                                </Button>
                            </div>
                        </Form>
                    </ModalBody>
                </ModalContent>
            </Modal>
        </>
    ) : null;
    const promptForTableRemoval = isInstalling && step === 3 && dbTables.length > 0 ? (
        <>
            <Modal isOpen={true}>
                <ModalContent>
                    <ModalHeader className="flex flex-col gap-1">Remove database tables?</ModalHeader>
                    <ModalBody>
                        <p>Installer detected that your database is not empty. The following database tables were detected:</p>
                        <ul>
                            {dbTables.map((item,key) => (
                                <li key={dbTables[key]}>{item}</li>
                            ))}
                        </ul>
                        <p>Processing with installation will remove all existing tables in selected database.</p>
                    </ModalBody>
                    <ModalFooter>
                        <Button className="bg-primary" isLoading={isRemoving} onPress={RemoveTables}>
                            Delete tables
                        </Button>
                    </ModalFooter>
                </ModalContent>
            </Modal>
        </>
    ) : null;
    const welcomeText = !isInstalling ? <p>Welcome to the BlogForge web installation script. This check will now perform DB Connection check.</p> : null;
    const installStatusText = isInstalling ? (
        <>
            <span>Install status:</span>
            <Textarea readOnly={true} value={installStatus.join("\n")}>
            </Textarea>
        </>
    ) : null;
    return (
        <div className="flex flex-col w-[100%] flex-wrap h-[30em] justify-center md:items-center">
            <Card className="flex w-[100%] md:w-[50%] flex-col flex-wrap items-start p-4">
                <div className="flex w-[100%] items-center justify-center flex-wrap">
                    <h2 className="font-bold">Current status: {headingText}</h2>
                </div>
                <span>Step {step} / 7</span>
                {welcomeText}
                {installStatusText}
                {promptForDBDetails}
                {promptForTableRemoval}
                <div className="flex flex-row flex-wrap w-[100%] justify-center">
                    <Button className="bg-primary" isLoading={isLoading} onPress={onClickEvent}>
                        <span className="text-white">Continue</span>
                    </Button>
                </div>
            </Card>
        </div>
    )
});
