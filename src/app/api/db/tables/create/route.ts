import getApiServerUrl from "@/server"
import {DBDropType} from "@/Types/DBDropType";
import {NextResponse} from "next/server";
export async function POST()
{
    const response = await fetch(getApiServerUrl+"/install/db/tables/create", {
        method:"POST"
    });
    switch(response.status) {
        default:
            const data :DBDropType = await response.json();
            return NextResponse.json(data);
    }
}
