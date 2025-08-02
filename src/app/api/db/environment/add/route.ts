import getApiServerUrl from '@/server'
import {NextApiRequest} from "next";
import {DBConnection} from "@/Types/DBConnection";
import {NextResponse} from "next/server";

export async function POST(req:Request)
{
    try {
        const data = await req.formData();
        console.log(data.get('dbhost'));
        const response = await fetch(getApiServerUrl+"/install/environment/update",{
            method:"POST",
            body:data
        });
        console.log(response);
        const responseData :DBConnection = await response.json();
        if(responseData.success){
            const r2 = await fetch(getApiServerUrl+"/install/connection");
            const data2 :DBConnection = await r2.json();
            if(data2.success){
                return NextResponse.json({success:true, message:"Successfully validated database connection"});
            } else {
                return NextResponse.json(data2);
            }
        } else {
            return NextResponse.json(responseData);
        }
    } catch(error) {
        console.log(error);
        return NextResponse.json({hasErrors:true, message: error.errorText});
    }
}
