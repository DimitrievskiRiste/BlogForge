import getApiServerUrl from "@/server";
import {DBConnection} from "@/Types/DBConnection";
import {NextResponse} from "next/server";
export async function POST()
{
   try {
       const response = await fetch(getApiServerUrl+"/install/db/groups/create",{method:"POST"});
       switch(response.status){
           default:
               const data :DBConnection = await response.json();
               return NextResponse.json(data);
       }
   } catch(error) {
       console.error(error);
       return NextResponse.json({hasErrors:true, message:"Something is wrong. Can't insert default user groups."});
   }
}
